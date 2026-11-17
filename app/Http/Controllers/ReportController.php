<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Expense;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'profit-loss');
        $dateFrom = $request->query('date_from', Carbon::today()->startOfMonth()->toDateString());
        $dateTo = $request->query('date_to', Carbon::today()->endOfMonth()->toDateString());

        // Prepare data specific to each active report tab
        $reportData = match ($tab) {
            'sales-daily' => $this->getDailySalesReport($dateFrom, $dateTo),
            'sales-monthly' => $this->getMonthlySalesReport(),
            'sales-datewise' => $this->getDatewiseSalesReport($dateFrom, $dateTo),
            'payments' => $this->getPaymentsReport($dateFrom, $dateTo),
            'outstanding' => $this->getOutstandingBalancesReport(),
            'expenses-daily' => $this->getDailyExpensesReport($dateFrom, $dateTo),
            'expenses-monthly' => $this->getMonthlyExpensesReport(),
            'booking-profit' => $this->getBookingProfitReport($dateFrom, $dateTo),
            'cancelled' => $this->getCancelledBookingsReport($dateFrom, $dateTo),
            default => $this->getOverallProfitLossReport($dateFrom, $dateTo),
        };

        return view('reports.index', compact('tab', 'dateFrom', 'dateTo', 'reportData'));
    }

    // 1. Daily Sales Report
    protected function getDailySalesReport($dateFrom, $dateTo)
    {
        return Booking::with(['customer', 'eventType'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereBetween('event_date', [$dateFrom, $dateTo])
            ->orderBy('event_date', 'desc')
            ->get();
    }

    // 2. Monthly Sales Report
    protected function getMonthlySalesReport()
    {
        $dateFormat = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', event_date)"
            : "DATE_FORMAT(event_date, '%Y-%m')";

        return Booking::whereIn('status', ['confirmed', 'completed'])
            ->selectRaw("{$dateFormat} as month, COUNT(id) as total_events, SUM(total_amount) as gross_amount, SUM(discount) as total_discount, SUM(final_amount) as total_sales")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }

    // 3. Date-wise Sales Report
    protected function getDatewiseSalesReport($dateFrom, $dateTo)
    {
        return Booking::with(['customer', 'eventType', 'venue', 'decorationCategory'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereBetween('event_date', [$dateFrom, $dateTo])
            ->orderBy('event_date')
            ->get();
    }

    // 4. Total Payments Received Report
    protected function getPaymentsReport($dateFrom, $dateTo)
    {
        return BookingPayment::with(['customer', 'booking'])
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    // 5. Outstanding Customer Balances Report
    protected function getOutstandingBalancesReport()
    {
        return Booking::with(['customer', 'eventType'])
            ->whereIn('status', ['confirmed', 'tentative'])
            ->get()
            ->filter(fn($b) => $b->remaining_balance > 0)
            ->values();
    }

    // 6. Daily Expense Report
    protected function getDailyExpensesReport($dateFrom, $dateTo)
    {
        return Expense::with(['category', 'booking'])
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->orderBy('expense_date', 'desc')
            ->get();
    }

    // 7. Monthly Expense Report
    protected function getMonthlyExpensesReport()
    {
        $dateFormat = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', expense_date)"
            : "DATE_FORMAT(expense_date, '%Y-%m')";

        return Expense::selectRaw("{$dateFormat} as month, COUNT(id) as total_count, SUM(amount) as total_expense")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();
    }

    // 8. Booking-wise Profit Report
    protected function getBookingProfitReport($dateFrom, $dateTo)
    {
        return Booking::with(['customer', 'eventType', 'expenses'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereBetween('event_date', [$dateFrom, $dateTo])
            ->orderBy('event_date', 'desc')
            ->get();
    }

    // 9. Overall Profit / Loss & Cash Movement Report
    protected function getOverallProfitLossReport($dateFrom, $dateTo)
    {
        // Eligible Sales in Date Range
        $eligibleBookings = Booking::whereIn('status', ['confirmed', 'completed'])
            ->whereBetween('event_date', [$dateFrom, $dateTo])
            ->get();

        $totalSales = (float) $eligibleBookings->sum('final_amount');
        $eligibleBookingCount = $eligibleBookings->count();

        // Expenses in Date Range
        $expenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->get();
        $bookingDirectExpenses = (float) $expenses->whereNotNull('booking_id')->sum('amount');
        $generalOverheadExpenses = (float) $expenses->whereNull('booking_id')->sum('amount');
        $totalExpenses = $bookingDirectExpenses + $generalOverheadExpenses;

        // Sales-Based Net Profit = Sales - All Expenses
        $accrualNetProfit = $totalSales - $totalExpenses;

        // Cash Flow / Cash Movement
        $payments = BookingPayment::whereBetween('payment_date', [$dateFrom, $dateTo])->get();
        $actualReceipts = (float) $payments->where('is_refund', false)->sum('amount');
        $refundsPaid = (float) $payments->where('is_refund', true)->sum('amount');
        $netCashReceived = $actualReceipts - $refundsPaid;

        // Net Cash Movement = Net Cash Received - Actual Expenses Paid
        $netCashMovement = $netCashReceived - $totalExpenses;

        return [
            'totalSales' => $totalSales,
            'eligibleBookingCount' => $eligibleBookingCount,
            'bookingDirectExpenses' => $bookingDirectExpenses,
            'generalOverheadExpenses' => $generalOverheadExpenses,
            'totalExpenses' => $totalExpenses,
            'accrualNetProfit' => $accrualNetProfit,
            'actualReceipts' => $actualReceipts,
            'refundsPaid' => $refundsPaid,
            'netCashReceived' => $netCashReceived,
            'netCashMovement' => $netCashMovement,
        ];
    }

    // 10. Cancelled Booking Report
    protected function getCancelledBookingsReport($dateFrom, $dateTo)
    {
        return Booking::with(['customer', 'eventType', 'payments'])
            ->where('status', 'cancelled')
            ->whereBetween('event_date', [$dateFrom, $dateTo])
            ->latest('event_date')
            ->get();
    }
}
