<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\EventType;
use App\Models\Venue;
use App\Models\DecorationCategory;
use App\Models\BookingPayment;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $bookingType = $request->query('booking_type');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $search = $request->query('search');

        $bookings = Booking::with(['customer', 'eventType', 'venue', 'decorationCategory'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($bookingType, fn($q) => $q->where('booking_type', $bookingType))
            ->when($dateFrom, fn($q) => $q->whereDate('event_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('event_date', '<=', $dateTo))
            ->when($search, function ($q, $search) {
                $q->where('booking_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('mobile_number', 'like', "%{$search}%");
                    });
            })
            ->latest('event_date')
            ->paginate(15)
            ->withQueryString();

        return view('bookings.index', compact('bookings', 'status', 'bookingType', 'dateFrom', 'dateTo', 'search'));
    }

    public function create(Request $request)
    {
        $selectedCustomerId = $request->query('customer_id');
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $eventTypes = EventType::where('is_active', true)->orderBy('name')->get();
        $venues = Venue::where('is_active', true)->orderBy('name')->get();
        $decorationCategories = DecorationCategory::where('is_active', true)->orderBy('name')->get();
        $generatedBookingNumber = Booking::generateBookingNumber();

        return view('bookings.create', compact(
            'customers',
            'eventTypes',
            'venues',
            'decorationCategories',
            'selectedCustomerId',
            'generatedBookingNumber'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'booking_date' => ['required', 'date'],
            'event_date' => ['required', 'date'],
            'event_start_time' => ['nullable'],
            'event_end_time' => ['nullable'],
            'event_type_id' => ['required', 'exists:event_types,id'],
            'booking_type' => ['required', 'in:indoor,outdoor'],
            'venue_id' => ['nullable', 'exists:venues,id'],
            'venue_address' => ['nullable', 'string'],
            'decoration_category_id' => ['nullable', 'exists:decoration_categories,id'],
            'decoration_details' => ['nullable', 'string'],
            'special_instructions' => ['nullable', 'string'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'advance_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'in:cash,bank,online_transfer,cheque,other'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:tentative,confirmed'],
        ]);

        $totalAmount = (float) $validated['total_amount'];
        $discount = (float) ($validated['discount'] ?? 0);
        $finalAmount = max(0, $totalAmount - $discount);
        $advanceAmount = (float) ($validated['advance_amount'] ?? 0);

        if ($advanceAmount > $finalAmount) {
            return back()->withInput()->withErrors([
                'advance_amount' => 'Advance payment cannot exceed the final booking amount (' . number_format($finalAmount, 2) . ').',
            ]);
        }

        // Check for overlapping resource warnings
        $overlapWarning = $this->checkResourceOverlap(
            $validated['event_date'],
            $validated['venue_id'] ?? null,
            $validated['decoration_category_id'] ?? null,
            $validated['event_start_time'] ?? null,
            $validated['event_end_time'] ?? null
        );

        return DB::transaction(function () use ($validated, $totalAmount, $discount, $finalAmount, $advanceAmount, $overlapWarning) {
            $booking = Booking::create([
                'booking_number' => Booking::generateBookingNumber(),
                'customer_id' => $validated['customer_id'],
                'event_type_id' => $validated['event_type_id'],
                'venue_id' => $validated['venue_id'] ?? null,
                'decoration_category_id' => $validated['decoration_category_id'] ?? null,
                'booking_type' => $validated['booking_type'],
                'booking_date' => $validated['booking_date'],
                'event_date' => $validated['event_date'],
                'event_start_time' => $validated['event_start_time'] ?? null,
                'event_end_time' => $validated['event_end_time'] ?? null,
                'venue_address' => $validated['venue_address'] ?? null,
                'decoration_details' => $validated['decoration_details'] ?? null,
                'special_instructions' => $validated['special_instructions'] ?? null,
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'final_amount' => $finalAmount,
                'status' => $validated['status'],
                'created_by' => auth()->id(),
            ]);

            // If an advance payment was collected, record it immediately
            if ($advanceAmount > 0) {
                BookingPayment::create([
                    'receipt_number' => BookingPayment::generateReceiptNumber(),
                    'booking_id' => $booking->id,
                    'customer_id' => $booking->customer_id,
                    'payment_date' => $validated['booking_date'],
                    'amount' => $advanceAmount,
                    'payment_type' => 'advance',
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                    'transaction_reference' => $validated['transaction_reference'] ?? null,
                    'remarks' => 'Advance payment recorded at the time of booking creation.',
                    'received_by' => auth()->id(),
                    'is_refund' => false,
                ]);
            }

            $msg = "Booking {$booking->booking_number} created successfully.";
            if ($overlapWarning) {
                $msg .= " Notice: {$overlapWarning}";
            }

            return redirect()->route('bookings.show', $booking)->with('success', $msg);
        });
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'customer',
            'eventType',
            'venue',
            'decorationCategory',
            'payments.receiver',
            'expenses.category',
            'creator'
        ]);

        $overlapWarning = $this->checkResourceOverlap(
            $booking->event_date->format('Y-m-d'),
            $booking->venue_id,
            $booking->decoration_category_id,
            $booking->event_start_time,
            $booking->event_end_time,
            $booking->id
        );

        return view('bookings.show', compact('booking', 'overlapWarning'));
    }

    public function edit(Booking $booking)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $eventTypes = EventType::where('is_active', true)->orderBy('name')->get();
        $venues = Venue::where('is_active', true)->orderBy('name')->get();
        $decorationCategories = DecorationCategory::where('is_active', true)->orderBy('name')->get();

        $isFinancialLocked = in_array($booking->status, ['completed', 'cancelled']);

        return view('bookings.edit', compact(
            'booking',
            'customers',
            'eventTypes',
            'venues',
            'decorationCategories',
            'isFinancialLocked'
        ));
    }

    public function update(Request $request, Booking $booking)
    {
        $isFinancialLocked = in_array($booking->status, ['completed', 'cancelled']);

        $rules = [
            'booking_date' => ['required', 'date'],
            'event_date' => ['required', 'date'],
            'event_start_time' => ['nullable'],
            'event_end_time' => ['nullable'],
            'event_type_id' => ['required', 'exists:event_types,id'],
            'booking_type' => ['required', 'in:indoor,outdoor'],
            'venue_id' => ['nullable', 'exists:venues,id'],
            'venue_address' => ['nullable', 'string'],
            'decoration_category_id' => ['nullable', 'exists:decoration_categories,id'],
            'decoration_details' => ['nullable', 'string'],
            'special_instructions' => ['nullable', 'string'],
            'status' => ['required', 'in:tentative,confirmed,completed,cancelled'],
            'cancellation_reason' => ['nullable', 'string', 'required_if:status,cancelled'],
        ];

        if (!$isFinancialLocked) {
            $rules['total_amount'] = ['required', 'numeric', 'min:0'];
            $rules['discount'] = ['nullable', 'numeric', 'min:0'];
        }

        $validated = $request->validate($rules);

        return DB::transaction(function () use ($validated, $booking, $isFinancialLocked) {
            if (!$isFinancialLocked) {
                $totalAmount = (float) $validated['total_amount'];
                $discount = (float) ($validated['discount'] ?? 0);
                $finalAmount = max(0, $totalAmount - $discount);

                $booking->total_amount = $totalAmount;
                $booking->discount = $discount;
                $booking->final_amount = $finalAmount;
            }

            if ($validated['status'] === 'cancelled' && $booking->status !== 'cancelled') {
                $booking->cancelled_at = now();
                $booking->cancellation_reason = $validated['cancellation_reason'] ?? 'Cancelled by user';
            } elseif ($validated['status'] !== 'cancelled') {
                $booking->cancelled_at = null;
                $booking->cancellation_reason = null;
            }

            $booking->event_type_id = $validated['event_type_id'];
            $booking->venue_id = $validated['venue_id'] ?? null;
            $booking->decoration_category_id = $validated['decoration_category_id'] ?? null;
            $booking->booking_type = $validated['booking_type'];
            $booking->booking_date = $validated['booking_date'];
            $booking->event_date = $validated['event_date'];
            $booking->event_start_time = $validated['event_start_time'] ?? null;
            $booking->event_end_time = $validated['event_end_time'] ?? null;
            $booking->venue_address = $validated['venue_address'] ?? null;
            $booking->decoration_details = $validated['decoration_details'] ?? null;
            $booking->special_instructions = $validated['special_instructions'] ?? null;
            $booking->status = $validated['status'];

            $booking->save();

            return redirect()->route('bookings.show', $booking)->with('success', 'Booking updated successfully.');
        });
    }

    public function cancel(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancelled_at' => now(),
        ]);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking marked as cancelled.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:tentative,confirmed,completed,cancelled'],
            'cancellation_reason' => ['nullable', 'string', 'required_if:status,cancelled'],
        ]);

        $updateData = ['status' => $validated['status']];
        if ($validated['status'] === 'cancelled') {
            $updateData['cancelled_at'] = now();
            $updateData['cancellation_reason'] = $validated['cancellation_reason'] ?? 'Cancelled';
        } else {
            $updateData['cancelled_at'] = null;
            $updateData['cancellation_reason'] = null;
        }

        $booking->update($updateData);

        return back()->with('success', "Booking status updated to {$validated['status']}.");
    }

    public function confirmationSlip(Booking $booking)
    {
        $booking->load(['customer', 'eventType', 'venue', 'decorationCategory', 'payments', 'creator']);
        $settings = BusinessSetting::current();

        return view('bookings.confirmation_slip', compact('booking', 'settings'));
    }

    /**
     * Checks if another active booking exists on the same event_date with resource overlap.
     * Non-blocking warning only.
     */
    protected function checkResourceOverlap($eventDate, $venueId, $decorationCategoryId, $startTime = null, $endTime = null, $excludeBookingId = null): ?string
    {
        if (!$venueId && !$decorationCategoryId) {
            return null;
        }

        $query = Booking::whereDate('event_date', $eventDate)
            ->whereIn('status', ['confirmed', 'tentative'])
            ->when($excludeBookingId, fn($q) => $q->where('id', '!=', $excludeBookingId));

        $warnings = [];

        // Venue overlap
        if ($venueId) {
            $venueConflict = (clone $query)->where('venue_id', $venueId)->first();
            if ($venueConflict) {
                $warnings[] = "Another booking ({$venueConflict->booking_number}) is already scheduled at this venue on {$eventDate}.";
            }
        }

        // Decoration Category overlap
        if ($decorationCategoryId) {
            $decorConflict = (clone $query)->where('decoration_category_id', $decorationCategoryId)->first();
            if ($decorConflict) {
                $warnings[] = "Another booking ({$decorConflict->booking_number}) is using the same decoration category on {$eventDate}.";
            }
        }

        return !empty($warnings) ? implode(' ', $warnings) : null;
    }
}
