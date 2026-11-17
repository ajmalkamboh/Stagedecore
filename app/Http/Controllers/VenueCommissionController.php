<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Venue;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;

class VenueCommissionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $venueId = $request->query('venue_id');
        $status = $request->query('status', 'all'); // all, pending, paid
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $venues = Venue::where('is_active', true)->orderBy('name')->get();

        $query = Booking::with(['venue', 'customer', 'eventType', 'commissionExpense'])
            ->where('has_commission', true)
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('booking_number', 'like', "%{$search}%")
                        ->orWhere('commission_recipient', 'like', "%{$search}%")
                        ->orWhereHas('venue', fn($vq) => $vq->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($venueId, fn($q) => $q->where('venue_id', $venueId))
            ->when($status !== 'all', fn($q) => $q->where('commission_status', $status))
            ->when($dateFrom, fn($q) => $q->whereDate('event_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('event_date', '<=', $dateTo));

        $commissions = (clone $query)
            ->orderBy('event_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Calculate Global KPI Summary Figures
        $totalCommission = Booking::where('has_commission', true)->sum('commission_amount');
        $paidCommission = Booking::where('has_commission', true)->where('commission_status', 'paid')->sum('commission_paid_amount');
        $pendingCommission = Booking::where('has_commission', true)->where('commission_status', 'pending')->sum('commission_amount');
        $venuesWithPending = Booking::where('has_commission', true)->where('commission_status', 'pending')->distinct('venue_id')->count('venue_id');

        return view('commissions.index', compact(
            'commissions',
            'venues',
            'search',
            'venueId',
            'status',
            'dateFrom',
            'dateTo',
            'totalCommission',
            'paidCommission',
            'pendingCommission',
            'venuesWithPending'
        ));
    }

    public function pay(Request $request, Booking $booking)
    {
        if (!$booking->has_commission) {
            return back()->with('error', 'This booking does not have a venue commission configured.');
        }

        if ($booking->commission_status === 'paid') {
            return back()->with('error', 'This commission has already been recorded as paid.');
        }

        $validated = $request->validate([
            'paid_amount' => ['required', 'numeric', 'min:0.01'],
            'paid_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank,online_transfer,cheque,other'],
            'recipient' => ['required', 'string', 'max:191'],
            'notes' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($validated, $booking) {
            // Find or create 'Venue Commission / Referral Fee' expense category
            $category = ExpenseCategory::firstOrCreate(
                ['name' => 'Venue Commission / Referral Fee'],
                [
                    'description' => 'Referral commissions and cuts paid to halls, marquees, and farmhouses.',
                    'is_active' => true,
                ]
            );

            // Record into Expense model
            $expense = Expense::create([
                'expense_number' => Expense::generateExpenseNumber(),
                'expense_category_id' => $category->id,
                'booking_id' => $booking->id,
                'amount' => $validated['paid_amount'],
                'expense_date' => $validated['paid_date'],
                'payment_method' => $validated['payment_method'],
                'description' => "Venue Commission paid to {$validated['recipient']} for " . ($booking->venue?->name ?? 'Venue') . " on booking {$booking->booking_number}",
                'remarks' => $validated['notes'] ?? 'Venue referral commission payout.',
                'recorded_by' => auth()->id(),
            ]);

            // Update booking commission record
            $booking->update([
                'commission_status' => 'paid',
                'commission_paid_amount' => $validated['paid_amount'],
                'commission_paid_at' => $validated['paid_date'],
                'commission_payment_method' => $validated['payment_method'],
                'commission_recipient' => $validated['recipient'],
                'commission_notes' => $validated['notes'],
                'commission_expense_id' => $expense->id,
            ]);

            return back()->with('success', "Commission of Rs. " . number_format($validated['paid_amount'], 2) . " paid successfully to {$validated['recipient']}.");
        });
    }

    public function voucher(Booking $booking)
    {
        if (!$booking->has_commission) {
            abort(404, 'No commission record found for this booking.');
        }

        $booking->load(['venue', 'customer', 'eventType', 'commissionExpense.recorder']);
        $businessSetting = BusinessSetting::current();

        return view('commissions.voucher', compact('booking', 'businessSetting'));
    }

    public function report(Request $request)
    {
        $venueId = $request->query('venue_id');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $venuesList = Venue::where('is_active', true)->orderBy('name')->get();

        // Venue-wise aggregate query
        $venueStats = Venue::withCount(['commissionBookings as total_events'])
            ->with(['commissionBookings' => function ($q) use ($dateFrom, $dateTo) {
                $q->when($dateFrom, fn($sq) => $sq->whereDate('event_date', '>=', $dateFrom))
                  ->when($dateTo, fn($sq) => $sq->whereDate('event_date', '<=', $dateTo));
            }])
            ->when($venueId, fn($q) => $q->where('id', $venueId))
            ->get()
            ->map(function ($venue) {
                $bookings = $venue->commissionBookings;
                $totalRevenue = $bookings->sum('final_amount');
                $totalCommission = $bookings->sum('commission_amount');
                $paidCommission = $bookings->where('commission_status', 'paid')->sum('commission_paid_amount');
                $pendingCommission = $bookings->where('commission_status', 'pending')->sum('commission_amount');

                return (object) [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'venue_type' => $venue->venue_type,
                    'contact_number' => $venue->contact_number,
                    'total_events' => $bookings->count(),
                    'total_revenue' => $totalRevenue,
                    'total_commission' => $totalCommission,
                    'paid_commission' => $paidCommission,
                    'pending_commission' => $pendingCommission,
                    'bookings' => $bookings,
                ];
            })
            ->filter(fn($v) => $v->total_events > 0 || $v->id == $venueId)
            ->values();

        // Overall Totals
        $grandTotalRevenue = $venueStats->sum('total_revenue');
        $grandTotalCommission = $venueStats->sum('total_commission');
        $grandTotalPaid = $venueStats->sum('paid_commission');
        $grandTotalPending = $venueStats->sum('pending_commission');

        return view('commissions.report', compact(
            'venueStats',
            'venuesList',
            'venueId',
            'dateFrom',
            'dateTo',
            'grandTotalRevenue',
            'grandTotalCommission',
            'grandTotalPaid',
            'grandTotalPending'
        ));
    }
}
