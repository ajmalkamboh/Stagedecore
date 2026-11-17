<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingPayment;
use App\Models\Expense;
use App\Models\Customer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Booking statistics
        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $tentativeBookings = Booking::where('status', 'tentative')->count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();

        // Events
        $todaysEventsCount = Booking::whereDate('event_date', $today)->count();
        $todaysEvents = Booking::with(['customer', 'eventType', 'venue'])
            ->whereDate('event_date', $today)
            ->orderBy('event_start_time')
            ->get();

        $upcomingEvents = Booking::with(['customer', 'eventType', 'venue', 'decorationCategory'])
            ->whereDate('event_date', '>', $today)
            ->whereIn('status', ['confirmed', 'tentative'])
            ->orderBy('event_date')
            ->orderBy('event_start_time')
            ->take(6)
            ->get();

        // Financial metrics (Strictly from real DB records)
        // Eligible Sales: Confirmed & Completed bookings (excluding cancelled and tentative)
        $totalSales = (float) Booking::whereIn('status', ['confirmed', 'completed'])->sum('final_amount');

        // Total payments received (excluding refunds)
        $totalPaymentsReceived = (float) BookingPayment::where('is_refund', false)->sum('amount');
        $totalRefundsPaid = (float) BookingPayment::where('is_refund', true)->sum('amount');
        $netCashReceived = max(0, $totalPaymentsReceived - $totalRefundsPaid);

        // Pending customer balances for eligible bookings
        $eligibleBookings = Booking::whereIn('status', ['confirmed', 'tentative'])->get();
        $pendingCustomerBalances = $eligibleBookings->sum(function ($b) {
            return $b->remaining_balance;
        });

        // Expenses
        $bookingExpenses = (float) Expense::whereNotNull('booking_id')->sum('amount');
        $generalExpenses = (float) Expense::whereNull('booking_id')->sum('amount');
        $totalExpenses = $bookingExpenses + $generalExpenses;

        // Estimated Net Profit = Eligible Sales - All Expenses
        $estimatedNetProfit = $totalSales - $totalExpenses;

        // Net Cash Flow = Net Cash Received - Total Expenses Paid
        $netCashMovement = $netCashReceived - $totalExpenses;

        // Recent Bookings
        $recentBookings = Booking::with(['customer', 'eventType', 'venue', 'decorationCategory'])
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard.index', compact(
            'totalBookings',
            'confirmedBookings',
            'tentativeBookings',
            'completedBookings',
            'cancelledBookings',
            'todaysEventsCount',
            'todaysEvents',
            'upcomingEvents',
            'totalSales',
            'totalPaymentsReceived',
            'netCashReceived',
            'pendingCustomerBalances',
            'totalExpenses',
            'bookingExpenses',
            'generalExpenses',
            'estimatedNetProfit',
            'netCashMovement',
            'recentBookings'
        ));
    }
}
