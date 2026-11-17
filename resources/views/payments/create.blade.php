@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-auto">
                <h5 class="fs-9 mb-0">
                    <span class="fas fa-hand-holding-usd text-success me-2"></span>Record Customer Payment
                </h5>
                <div class="fs-11 text-500">Auto-Receipt Number: <span class="badge badge-subtle-success fw-bold">{{ $generatedReceiptNumber }}</span></div>
            </div>
            <div class="col-auto">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('payments.index') }}">
                    <span class="fas fa-arrow-left me-1"></span> Back to Payments
                </a>
            </div>
        </div>
    </div>
    <div class="card-body bg-body-tertiary">
        <form action="{{ route('payments.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <!-- Booking Selection -->
                <div class="col-md-7">
                    <label class="form-label fw-bold" for="booking_id">Select Booking <span class="text-danger">*</span></label>
                    <select class="form-select @error('booking_id') is-invalid @enderror" id="booking_id" name="booking_id" required>
                        <option value="">-- Choose Booking --</option>
                        @foreach($bookings as $b)
                        <option value="{{ $b->id }}"
                            data-customer="{{ $b->customer->name }} ({{ $b->customer->mobile_number }})"
                            data-final="{{ $b->final_amount }}"
                            data-paid="{{ $b->total_paid }}"
                            data-balance="{{ $b->remaining_balance }}"
                            {{ (old('booking_id', $selectedBooking->id ?? '') == $b->id) ? 'selected' : '' }}>
                            {{ $b->booking_number }} - {{ $b->customer->name }} (Bal: {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($b->remaining_balance, 0) }}) - Event: {{ $b->event_date->format('d M') }}
                        </option>
                        @endforeach
                    </select>
                    @error('booking_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Payment Date -->
                <div class="col-md-5">
                    <label class="form-label fw-bold" for="payment_date">Payment Date <span class="text-danger">*</span></label>
                    <input class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required />
                    @error('payment_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Selected Booking Info Card -->
                <div class="col-12" id="bookingInfoCard" style="display: none;">
                    <div class="card border border-primary bg-light">
                        <div class="card-body p-3">
                            <div class="row text-center text-md-start">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <div class="fs-11 text-500 text-uppercase fw-bold">Customer</div>
                                    <div class="fs-9 fw-bold text-primary" id="cardCustomer"></div>
                                </div>
                                <div class="col-md-3 mb-2 mb-md-0">
                                    <div class="fs-11 text-500 text-uppercase fw-bold">Final Billed</div>
                                    <div class="fs-9 fw-bold text-900" id="cardFinal"></div>
                                </div>
                                <div class="col-md-2 mb-2 mb-md-0">
                                    <div class="fs-11 text-500 text-uppercase fw-bold">Already Paid</div>
                                    <div class="fs-9 fw-bold text-success" id="cardPaid"></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="fs-11 text-500 text-uppercase fw-bold">Outstanding Balance</div>
                                    <div class="fs-9 fw-bolder text-danger" id="cardBalance"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Amount -->
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="amount">Payment Amount ({{ $globalSettings->currency_symbol ?? 'Rs.' }}) <span class="text-danger">*</span></label>
                    <input class="form-control @error('amount') is-invalid @enderror" id="amount" type="number" step="0.01" name="amount" value="{{ old('amount') }}" placeholder="0.00" required />
                    @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Payment Type -->
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="payment_type">Payment Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('payment_type') is-invalid @enderror" id="payment_type" name="payment_type" required>
                        <option value="advance" {{ old('payment_type') == 'advance' ? 'selected' : '' }}>Advance Payment</option>
                        <option value="partial" {{ old('payment_type', 'partial') == 'partial' ? 'selected' : '' }}>Partial / Running Payment</option>
                        <option value="final" {{ old('payment_type') == 'final' ? 'selected' : '' }}>Final / Clearance Payment</option>
                        <option value="refund" {{ old('payment_type') == 'refund' ? 'selected' : '' }}>Refund / Return</option>
                    </select>
                    @error('payment_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="payment_method">Payment Method <span class="text-danger">*</span></label>
                    <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Account Deposit</option>
                        <option value="online_transfer" {{ old('payment_method') == 'online_transfer' ? 'selected' : '' }}>Online Transfer (JazzCash/Easypaisa/Raast)</option>
                        <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Transaction Reference -->
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="transaction_reference">Transaction Reference / Receipt #</label>
                    <input class="form-control @error('transaction_reference') is-invalid @enderror" id="transaction_reference" type="text" name="transaction_reference" value="{{ old('transaction_reference') }}" placeholder="Bank deposit slip # / Raast TID / Cheque #" />
                </div>

                <!-- Remarks -->
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="remarks">Payment Remarks / Notes</label>
                    <input class="form-control @error('remarks') is-invalid @enderror" id="remarks" type="text" name="remarks" value="{{ old('remarks') }}" placeholder="e.g. Received by Ali at office, 2nd installment" />
                </div>

                <div class="col-12 text-end mt-4">
                    <button class="btn btn-success px-5 py-2 fs-10 fw-bold" type="submit">
                        <span class="fas fa-check-circle me-1"></span> Save &amp; Generate Payment Receipt
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingSelect = document.getElementById('booking_id');
    const infoCard = document.getElementById('bookingInfoCard');
    const cardCustomer = document.getElementById('cardCustomer');
    const cardFinal = document.getElementById('cardFinal');
    const cardPaid = document.getElementById('cardPaid');
    const cardBalance = document.getElementById('cardBalance');
    const amountInput = document.getElementById('amount');
    const typeSelect = document.getElementById('payment_type');

    function updateBookingInfo() {
        if (!bookingSelect.value) {
            infoCard.style.display = 'none';
            return;
        }

        const opt = bookingSelect.options[bookingSelect.selectedIndex];
        const customer = opt.getAttribute('data-customer');
        const finalAmt = parseFloat(opt.getAttribute('data-final') || 0);
        const paidAmt = parseFloat(opt.getAttribute('data-paid') || 0);
        const balAmt = parseFloat(opt.getAttribute('data-balance') || 0);

        cardCustomer.textContent = customer;
        cardFinal.textContent = "{{ $globalSettings->currency_symbol ?? 'Rs.' }} " + finalAmt.toLocaleString('en-US', {minimumFractionDigits: 2});
        cardPaid.textContent = "{{ $globalSettings->currency_symbol ?? 'Rs.' }} " + paidAmt.toLocaleString('en-US', {minimumFractionDigits: 2});
        cardBalance.textContent = "{{ $globalSettings->currency_symbol ?? 'Rs.' }} " + balAmt.toLocaleString('en-US', {minimumFractionDigits: 2});

        infoCard.style.display = 'block';

        // Pre-fill amount with remaining balance if empty
        if (!amountInput.value || parseFloat(amountInput.value) === 0) {
            amountInput.value = balAmt > 0 ? balAmt : '';
            if (balAmt > 0 && paidAmt > 0) {
                typeSelect.value = 'final';
            }
        }
    }

    bookingSelect.addEventListener('change', updateBookingInfo);
    updateBookingInfo();
});
</script>
@endpush
