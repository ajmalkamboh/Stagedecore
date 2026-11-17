@extends('layouts.app')

@section('title', 'New Stage Booking')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-auto">
                <h5 class="fs-9 mb-0">
                    <span class="fas fa-calendar-plus text-primary me-2"></span>Create Stage Decoration Booking
                </h5>
                <div class="fs-11 text-500">Booking Reference: <span class="badge badge-subtle-primary fw-bold">{{ $generatedBookingNumber }}</span></div>
            </div>
            <div class="col-auto">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('bookings.index') }}">
                    <span class="fas fa-arrow-left me-1"></span> Back to Bookings
                </a>
            </div>
        </div>
    </div>
    <div class="card-body bg-body-tertiary">
        <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
            @csrf

            <!-- Section 1: Customer & Event Schedule -->
            <div class="card mb-3 border">
                <div class="card-header bg-200 py-2">
                    <h6 class="mb-0 fs-10 fw-bold text-700"><span class="fas fa-user me-2 text-primary"></span>1. Customer &amp; Schedule</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Customer -->
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold mb-0" for="customer_id">Select Customer <span class="text-danger">*</span></label>
                                <a href="{{ route('customers.create') }}" class="fs-11 text-primary" target="_blank">+ Add New Customer</a>
                            </div>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                <option value="">-- Choose Customer --</option>
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ (old('customer_id', $selectedCustomerId) == $c->id) ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->mobile_number }})
                                </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Booking Date -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold" for="booking_date">Booking Date <span class="text-danger">*</span></label>
                            <input class="form-control @error('booking_date') is-invalid @enderror" id="booking_date" type="date" name="booking_date" value="{{ old('booking_date', date('Y-m-d')) }}" required />
                            @error('booking_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Event Date -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold" for="event_date">Event Date <span class="text-danger">*</span></label>
                            <input class="form-control @error('event_date') is-invalid @enderror" id="event_date" type="date" name="event_date" value="{{ old('event_date') }}" required />
                            @error('event_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Event Timings -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold" for="event_start_time">Event Start Time</label>
                            <input class="form-control @error('event_start_time') is-invalid @enderror" id="event_start_time" type="time" name="event_start_time" value="{{ old('event_start_time', '19:00') }}" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold" for="event_end_time">Event End Time</label>
                            <input class="form-control @error('event_end_time') is-invalid @enderror" id="event_end_time" type="time" name="event_end_time" value="{{ old('event_end_time', '23:30') }}" />
                        </div>

                        <!-- Event Type -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold" for="event_type_id">Event Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('event_type_id') is-invalid @enderror" id="event_type_id" name="event_type_id" required>
                                <option value="">-- Choose Event Type --</option>
                                @foreach($eventTypes as $et)
                                <option value="{{ $et->id }}" {{ old('event_type_id') == $et->id ? 'selected' : '' }}>
                                    {{ $et->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('event_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Booking Type (Indoor / Outdoor) -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold" for="booking_type">Booking Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('booking_type') is-invalid @enderror" id="booking_type" name="booking_type" required>
                                <option value="indoor" {{ old('booking_type', 'indoor') == 'indoor' ? 'selected' : '' }}>Indoor (Hall / Hotel)</option>
                                <option value="outdoor" {{ old('booking_type') == 'outdoor' ? 'selected' : '' }}>Outdoor (Ground / Home / Lawn)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Venue & Decoration Specifications -->
            <div class="card mb-3 border">
                <div class="card-header bg-200 py-2">
                    <h6 class="mb-0 fs-10 fw-bold text-700"><span class="fas fa-map-marker-alt me-2 text-primary"></span>2. Venue &amp; Stage Decoration</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Venue Selection -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="venue_id">Registered Venue (Optional)</label>
                            <select class="form-select @error('venue_id') is-invalid @enderror" id="venue_id" name="venue_id">
                                <option value="">-- Or enter custom address below --</option>
                                @foreach($venues as $v)
                                <option value="{{ $v->id }}" data-address="{{ $v->address }}" {{ old('venue_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->name }} ({{ $v->venue_type }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Custom Address -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="venue_address">Venue Address / Specific Location</label>
                            <input class="form-control @error('venue_address') is-invalid @enderror" id="venue_address" type="text" name="venue_address" value="{{ old('venue_address') }}" placeholder="Specific hall name, lawn number, or private home address" />
                        </div>

                        <!-- Decoration Category -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="decoration_category_id">Stage Decoration Category</label>
                            <select class="form-select @error('decoration_category_id') is-invalid @enderror" id="decoration_category_id" name="decoration_category_id">
                                <option value="">-- Choose Category / Package --</option>
                                @foreach($decorationCategories as $dc)
                                <option value="{{ $dc->id }}" data-price="{{ $dc->base_price }}" {{ old('decoration_category_id') == $dc->id ? 'selected' : '' }}>
                                    {{ $dc->name }} (Base: {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($dc->base_price, 0) }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Booking Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="status">Initial Booking Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="tentative" {{ old('status', 'tentative') == 'tentative' ? 'selected' : '' }}>Tentative (Quotation / Soft Hold)</option>
                                <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed (Booked &amp; Scheduled)</option>
                            </select>
                        </div>

                        <!-- Decoration Details -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="decoration_details">Decoration Specifications &amp; Requirements</label>
                            <textarea class="form-control @error('decoration_details') is-invalid @enderror" id="decoration_details" name="decoration_details" rows="3" placeholder="Color theme (white &amp; gold, red roses, pastel), backdrop drape size, sofa style, walkway floral pillars...">{{ old('decoration_details') }}</textarea>
                        </div>

                        <!-- Special Instructions -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="special_instructions">Special Instructions / Logistics Notes</label>
                            <textarea class="form-control @error('special_instructions') is-invalid @enderror" id="special_instructions" name="special_instructions" rows="3" placeholder="Setup completion deadline, hall gate access, power generator requirement, disassembly notes...">{{ old('special_instructions') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Financial Information & Advance Payment -->
            <div class="card mb-3 border">
                <div class="card-header bg-200 py-2">
                    <h6 class="mb-0 fs-10 fw-bold text-700"><span class="fas fa-money-bill-wave me-2 text-success"></span>3. Financial Information &amp; Advance Payment</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Total Amount -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="total_amount">Total Booking Amount ({{ $globalSettings->currency_symbol ?? 'Rs.' }}) <span class="text-danger">*</span></label>
                            <input class="form-control @error('total_amount') is-invalid @enderror" id="total_amount" type="number" step="0.01" name="total_amount" value="{{ old('total_amount', '0') }}" required />
                            @error('total_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Discount -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="discount">Discount / Concession ({{ $globalSettings->currency_symbol ?? 'Rs.' }})</label>
                            <input class="form-control @error('discount') is-invalid @enderror" id="discount" type="number" step="0.01" name="discount" value="{{ old('discount', '0') }}" />
                            @error('discount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Final Amount Display -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="final_amount_display">Final Booking Amount</label>
                            <input class="form-control bg-light fw-bold text-primary fs-9" id="final_amount_display" type="text" readonly value="0.00" />
                        </div>

                        <div class="col-12"><hr class="my-1"></div>

                        <!-- Advance Payment -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="advance_amount">Advance Payment Received Now</label>
                            <input class="form-control @error('advance_amount') is-invalid @enderror" id="advance_amount" type="number" step="0.01" name="advance_amount" value="{{ old('advance_amount', '0') }}" />
                            <div class="fs-11 text-500">Leave 0 if payment will be collected later</div>
                            @error('advance_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="payment_method">Payment Method</label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method">
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Account Deposit</option>
                                <option value="online_transfer" {{ old('payment_method') == 'online_transfer' ? 'selected' : '' }}>Online Transfer (JazzCash/Easypaisa/Raast)</option>
                                <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Transaction Reference -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="transaction_reference">Transaction Reference / Receipt Remarks</label>
                            <input class="form-control @error('transaction_reference') is-invalid @enderror" id="transaction_reference" type="text" name="transaction_reference" value="{{ old('transaction_reference') }}" placeholder="Bank slip # / Raast TID / Cheque #" />
                        </div>

                        <!-- Remaining Balance Display -->
                        <div class="col-12 text-end bg-light p-3 rounded">
                            <span class="fs-10 text-uppercase fw-bold text-700 me-2">Calculated Remaining Customer Balance:</span>
                            <span class="fs-8 fw-bolder text-danger" id="remaining_balance_display">{{ $globalSettings->currency_symbol ?? 'Rs.' }} 0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-end">
                <button class="btn btn-primary px-5 py-2 fs-10 fw-bold" type="submit">
                    <span class="fas fa-check-circle me-1"></span> Confirm &amp; Save Booking
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document-ready:
document.addEventListener('DOMContentLoaded', function() {
    const totalInput = document.getElementById('total_amount');
    const discountInput = document.getElementById('discount');
    const advanceInput = document.getElementById('advance_amount');
    const finalDisplay = document.getElementById('final_amount_display');
    const balanceDisplay = document.getElementById('remaining_balance_display');
    const decorSelect = document.getElementById('decoration_category_id');
    const venueSelect = document.getElementById('venue_id');
    const venueAddressInput = document.getElementById('venue_address');

    // Auto-fill venue address if venue selected
    if (venueSelect) {
        venueSelect.addEventListener('change', function() {
            const selectedOpt = this.options[this.selectedIndex];
            const addr = selectedOpt.getAttribute('data-address');
            if (addr && !venueAddressInput.value) {
                venueAddressInput.value = addr;
            }
        });
    }

    // Auto-fill base price if category selected and total amount is 0
    if (decorSelect) {
        decorSelect.addEventListener('change', function() {
            const selectedOpt = this.options[this.selectedIndex];
            const basePrice = parseFloat(selectedOpt.getAttribute('data-price') || 0);
            if (basePrice > 0 && (parseFloat(totalInput.value) === 0 || !totalInput.value)) {
                totalInput.value = basePrice;
                recalculate();
            }
        });
    }

    function recalculate() {
        const total = parseFloat(totalInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const advance = parseFloat(advanceInput.value) || 0;

        const finalAmount = Math.max(0, total - discount);
        const remaining = Math.max(0, finalAmount - advance);

        finalDisplay.value = finalAmount.toFixed(2);
        balanceDisplay.textContent = "{{ $globalSettings->currency_symbol ?? 'Rs.' }} " + remaining.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    totalInput.addEventListener('input', recalculate);
    discountInput.addEventListener('input', recalculate);
    advanceInput.addEventListener('input', recalculate);

    recalculate();
});
</script>
@endpush
