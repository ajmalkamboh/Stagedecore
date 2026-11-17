<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingPayment;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $paymentType = $request->query('payment_type');
        $paymentMethod = $request->query('payment_method');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $payments = BookingPayment::with(['booking', 'customer', 'receiver'])
            ->when($search, function ($q, $search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                    ->orWhere('transaction_reference', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('mobile_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('booking', function ($bq) use ($search) {
                        $bq->where('booking_number', 'like', "%{$search}%");
                    });
            })
            ->when($paymentType, fn($q) => $q->where('payment_type', $paymentType))
            ->when($paymentMethod, fn($q) => $q->where('payment_method', $paymentMethod))
            ->when($dateFrom, fn($q) => $q->whereDate('payment_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('payment_date', '<=', $dateTo))
            ->latest('payment_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        // Totals
        $totalCollected = BookingPayment::where('is_refund', false)->sum('amount');
        $totalRefunded = BookingPayment::where('is_refund', true)->sum('amount');
        $netCollected = $totalCollected - $totalRefunded;

        return view('payments.index', compact(
            'payments',
            'search',
            'paymentType',
            'paymentMethod',
            'dateFrom',
            'dateTo',
            'totalCollected',
            'totalRefunded',
            'netCollected'
        ));
    }

    public function create(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $selectedBooking = null;

        if ($bookingId) {
            $selectedBooking = Booking::with('customer')->findOrFail($bookingId);
        }

        // Active bookings eligible for payment
        $bookings = Booking::with('customer')
            ->whereIn('status', ['confirmed', 'tentative', 'completed'])
            ->orderBy('id', 'desc')
            ->get();

        $generatedReceiptNumber = BookingPayment::generateReceiptNumber();

        return view('payments.create', compact('bookings', 'selectedBooking', 'generatedReceiptNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_type' => ['required', 'in:advance,partial,final,refund'],
            'payment_method' => ['required', 'in:cash,bank,online_transfer,cheque,other'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'remarks' => ['nullable', 'string'],
        ]);

        $booking = Booking::with('customer')->findOrFail($validated['booking_id']);
        $amount = (float) $validated['amount'];
        $isRefund = ($validated['payment_type'] === 'refund');

        if ($isRefund) {
            if ($amount > $booking->total_paid) {
                return back()->withInput()->withErrors([
                    'amount' => 'Refund amount cannot exceed the total amount already paid on this booking (' . number_format($booking->total_paid, 2) . ').',
                ]);
            }
        } else {
            if ($amount > $booking->remaining_balance) {
                return back()->withInput()->withErrors([
                    'amount' => 'Payment amount cannot exceed the booking remaining balance (' . number_format($booking->remaining_balance, 2) . ').',
                ]);
            }
        }

        return DB::transaction(function () use ($validated, $booking, $amount, $isRefund) {
            $payment = BookingPayment::create([
                'receipt_number' => BookingPayment::generateReceiptNumber(),
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'payment_date' => $validated['payment_date'],
                'amount' => $amount,
                'payment_type' => $validated['payment_type'],
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'received_by' => auth()->id(),
                'is_refund' => $isRefund,
            ]);

            return redirect()->route('payments.receipt', $payment)
                ->with('success', "Payment receipt {$payment->receipt_number} recorded successfully.");
        });
    }

    public function receipt(BookingPayment $payment)
    {
        $payment->load(['booking.eventType', 'booking.venue', 'customer', 'receiver']);
        $settings = BusinessSetting::current();

        return view('payments.receipt', compact('payment', 'settings'));
    }

    public function ledger(Request $request)
    {
        $customerId = $request->query('customer_id');
        $customers = Customer::orderBy('name')->get();
        $selectedCustomer = null;
        $ledgerEntries = collect();

        if ($customerId) {
            $selectedCustomer = Customer::with(['bookings', 'payments'])->findOrFail($customerId);

            // Build chronological ledger entries
            // 1. Debits: Booking confirmations (Amount billed)
            foreach ($selectedCustomer->bookings()->where('status', '!=', 'cancelled')->get() as $b) {
                $ledgerEntries->push([
                    'date' => $b->booking_date,
                    'type' => 'Booking Billed',
                    'reference' => $b->booking_number,
                    'description' => "Stage booking for {$b->eventType->name} on " . $b->event_date->format('d M Y'),
                    'debit' => (float) $b->final_amount,
                    'credit' => 0.00,
                    'link' => route('bookings.show', $b),
                ]);
            }

            // 2. Credits: Payments received (Credit) or Refunds (Debit)
            foreach ($selectedCustomer->payments as $p) {
                $ledgerEntries->push([
                    'date' => $p->payment_date,
                    'type' => $p->is_refund ? 'Payment Refund' : 'Payment Received',
                    'reference' => $p->receipt_number,
                    'description' => ucfirst($p->payment_type) . " payment via " . ucfirst(str_replace('_', ' ', $p->payment_method)) . ($p->transaction_reference ? " (Ref: {$p->transaction_reference})" : ""),
                    'debit' => $p->is_refund ? (float) $p->amount : 0.00,
                    'credit' => $p->is_refund ? 0.00 : (float) $p->amount,
                    'link' => route('payments.receipt', $p),
                ]);
            }

            // Sort chronologically
            $ledgerEntries = $ledgerEntries->sortBy('date')->values();

            // Calculate running balance
            $runningBalance = 0;
            $ledgerEntries = $ledgerEntries->map(function ($entry) use (&$runningBalance) {
                $runningBalance += ($entry['debit'] - $entry['credit']);
                $entry['balance'] = $runningBalance;
                return $entry;
            });
        }

        return view('payments.ledger', compact('customers', 'selectedCustomer', 'ledgerEntries', 'customerId'));
    }
}
