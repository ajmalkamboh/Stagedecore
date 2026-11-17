@extends('layouts.app')

@section('title', 'Edit Booking: ' . $booking->booking_number)

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-auto">
                <h5 class="fs-9 mb-0">
                    <span class="fas fa-edit text-primary me-2"></span>Edit Booking: {{ $booking->booking_number }}
                </h5>
                <div class="fs-11 text-500">Customer: <span class="fw-bold">{{ $booking->customer->name }}</span> ({{ $booking->customer->mobile_number }})</div>
            </div>
            <div class="col-auto">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('bookings.show', $booking) }}">
                    <span class="fas fa-arrow-left me-1"></span> Back to Details
                </a>
            </div>
        </div>
    </div>
    <div class="card-body bg-body-tertiary">
        @if($isFinancialLocked)
        <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
            <span class="fas fa-lock me-2 fs-3"></span>
            <div>
                <strong>Financial Records Protected:</strong> This booking has status <strong>{{ ucfirst($booking->status) }}</strong>. Financial amounts (Total &amp; Discount) are locked from accidental modification. Change the status back to Confirmed or Tentative if you need to adjust financial terms.
            </div>
        </div>
        @endif

        <form action="{{ route('bookings.update', $booking) }}" method="POST" id="bookingEditForm">
            @csrf
            @method('PUT')

            <!-- Section 1: Customer & Event Schedule -->
            <div class="card mb-3 border">
                <div class="card-header bg-200 py-2">
                    <h6 class="mb-0 fs-10 fw-bold text-700"><span class="fas fa-calendar-day me-2 text-primary"></span>1. Schedule &amp; Event Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold" for="booking_date">Booking Date <span class="text-danger">*</span></label>
                            <input class="form-control @error('booking_date') is-invalid @enderror" id="booking_date" type="date" name="booking_date" value="{{ old('booking_date', $booking->booking_date->format('Y-m-d')) }}" required />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold" for="event_date">Event Date <span class="text-danger">*</span></label>
                            <input class="form-control @error('event_date') is-invalid @enderror" id="event_date" type="date" name="event_date" value="{{ old('event_date', $booking->event_date->format('Y-m-d')) }}" required />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold" for="event_start_time">Start Time</label>
                            <input class="form-control @error('event_start_time') is-invalid @enderror" id="event_start_time" type="time" name="event_start_time" value="{{ old('event_start_time', $booking->event_start_time ? date('H:i', strtotime($booking->event_start_time)) : '') }}" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold" for="event_end_time">End Time</label>
                            <input class="form-control @error('event_end_time') is-invalid @enderror" id="event_end_time" type="time" name="event_end_time" value="{{ old('event_end_time', $booking->event_end_time ? date('H:i', strtotime($booking->event_end_time)) : '') }}" />
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="event_type_id">Event Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('event_type_id') is-invalid @enderror" id="event_type_id" name="event_type_id" required>
                                @foreach($eventTypes as $et)
                                <option value="{{ $et->id }}" {{ old('event_type_id', $booking->event_type_id) == $et->id ? 'selected' : '' }}>
                                    {{ $et->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="booking_type">Booking Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('booking_type') is-invalid @enderror" id="booking_type" name="booking_type" required>
                                <option value="indoor" {{ old('booking_type', $booking->booking_type) == 'indoor' ? 'selected' : '' }}>Indoor (Hall / Hotel)</option>
                                <option value="outdoor" {{ old('booking_type', $booking->booking_type) == 'outdoor' ? 'selected' : '' }}>Outdoor (Ground / Home / Lawn)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="status">Booking Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="tentative" {{ old('status', $booking->status) == 'tentative' ? 'selected' : '' }}>Tentative</option>
                                <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="completed" {{ old('status', $booking->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div class="col-12" id="cancellation_reason_box" style="display: {{ old('status', $booking->status) === 'cancelled' ? 'block' : 'none' }};">
                            <label class="form-label fw-bold text-danger" for="cancellation_reason">Cancellation Reason <span class="text-danger">*</span></label>
                            <input class="form-control @error('cancellation_reason') is-invalid @enderror" id="cancellation_reason" type="text" name="cancellation_reason" value="{{ old('cancellation_reason', $booking->cancellation_reason) }}" placeholder="Explain why booking was cancelled (client request, weather, date postponed...)" />
                            @error('cancellation_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Venue & Decoration -->
            <div class="card mb-3 border">
                <div class="card-header bg-200 py-2">
                    <h6 class="mb-0 fs-10 fw-bold text-700"><span class="fas fa-map-marker-alt me-2 text-primary"></span>2. Venue &amp; Stage Decoration</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="venue_id">Registered Venue (Optional)</label>
                            <select class="form-select @error('venue_id') is-invalid @enderror" id="venue_id" name="venue_id">
                                <option value="">-- Custom Venue / Unregistered --</option>
                                @foreach($venues as $v)
                                <option value="{{ $v->id }}" {{ old('venue_id', $booking->venue_id) == $v->id ? 'selected' : '' }}>
                                    {{ $v->name }} ({{ $v->venue_type }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="venue_address">Venue Address / Specific Location</label>
                            <input class="form-control @error('venue_address') is-invalid @enderror" id="venue_address" type="text" name="venue_address" value="{{ old('venue_address', $booking->venue_address) }}" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="decoration_category_id">Stage Decoration Category</label>
                            <select class="form-select @error('decoration_category_id') is-invalid @enderror" id="decoration_category_id" name="decoration_category_id">
                                <option value="">-- Choose Category / Package --</option>
                                @foreach($decorationCategories as $dc)
                                <option value="{{ $dc->id }}" {{ old('decoration_category_id', $booking->decoration_category_id) == $dc->id ? 'selected' : '' }}>
                                    {{ $dc->name }} (Base: {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($dc->base_price, 0) }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="decoration_details">Decoration Specifications</label>
                            <textarea class="form-control" id="decoration_details" name="decoration_details" rows="2">{{ old('decoration_details', $booking->decoration_details) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold" for="special_instructions">Special Instructions</label>
                            <textarea class="form-control" id="special_instructions" name="special_instructions" rows="2">{{ old('special_instructions', $booking->special_instructions) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Financial Details -->
            <div class="card mb-3 border">
                <div class="card-header bg-200 py-2">
                    <h6 class="mb-0 fs-10 fw-bold text-700"><span class="fas fa-money-bill-wave me-2 text-success"></span>3. Financial Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($isFinancialLocked)
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Total Amount</label>
                            <input class="form-control bg-light" type="text" readonly value="{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->total_amount, 2) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Discount</label>
                            <input class="form-control bg-light" type="text" readonly value="{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->discount, 2) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Final Amount</label>
                            <input class="form-control bg-light fw-bold text-primary" type="text" readonly value="{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->final_amount, 2) }}">
                        </div>
                        @else
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="total_amount">Total Amount ({{ $globalSettings->currency_symbol ?? 'Rs.' }}) <span class="text-danger">*</span></label>
                            <input class="form-control @error('total_amount') is-invalid @enderror" id="total_amount" type="number" step="0.01" name="total_amount" value="{{ old('total_amount', $booking->total_amount) }}" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="discount">Discount ({{ $globalSettings->currency_symbol ?? 'Rs.' }})</label>
                            <input class="form-control @error('discount') is-invalid @enderror" id="discount" type="number" step="0.01" name="discount" value="{{ old('discount', $booking->discount) }}" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Final Booking Amount</label>
                            <input class="form-control bg-light fw-bold text-primary" id="final_amount_display" type="text" readonly value="{{ number_format($booking->final_amount, 2) }}">
                        </div>
                        @endif

                        <div class="col-12 mt-3 text-end bg-light p-3 rounded">
                            <div class="d-inline-block text-start me-4">
                                <span class="fs-11 text-500 text-uppercase d-block">Total Paid:</span>
                                <span class="fs-10 fw-bold text-success">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->total_paid, 2) }}</span>
                            </div>
                            <div class="d-inline-block text-start">
                                <span class="fs-11 text-500 text-uppercase d-block">Current Balance:</span>
                                <span class="fs-9 fw-bolder {{ $booking->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->remaining_balance, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button class="btn btn-primary px-5 py-2 fs-10 fw-bold" type="submit">
                    <span class="fas fa-save me-1"></span> Update Booking
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const cancelBox = document.getElementById('cancellation_reason_box');
    const cancelInput = document.getElementById('cancellation_reason');

    if (statusSelect && cancelBox) {
        statusSelect.addEventListener('change', function() {
            if (this.value === 'cancelled') {
                cancelBox.style.display = 'block';
                cancelInput.setAttribute('required', 'required');
            } else {
                cancelBox.style.display = 'none';
                cancelInput.removeAttribute('required');
            }
        });
    }

    const totalInput = document.getElementById('total_amount');
    const discountInput = document.getElementById('discount');
    const finalDisplay = document.getElementById('final_amount_display');

    if (totalInput && discountInput && finalDisplay) {
        function recalculate() {
            const total = parseFloat(totalInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            const finalVal = Math.max(0, total - discount);
            finalDisplay.value = finalVal.toFixed(2);
        }
        totalInput.addEventListener('input', recalculate);
        discountInput.addEventListener('input', recalculate);
    }
});
</script>
@endpush
