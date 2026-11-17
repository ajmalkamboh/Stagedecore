@extends('layouts.app')

@section('title', 'Record Expense')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-auto">
                <h5 class="fs-9 mb-0">
                    <span class="fas fa-receipt text-danger me-2"></span>Record Expense
                </h5>
                <div class="fs-11 text-500">Auto-Expense Number: <span class="badge badge-subtle-danger fw-bold">{{ $generatedExpenseNumber }}</span></div>
            </div>
            <div class="col-auto">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('expenses.index') }}">
                    <span class="fas fa-arrow-left me-1"></span> Back to Expenses
                </a>
            </div>
        </div>
    </div>
    <div class="card-body bg-body-tertiary">
        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <!-- Expense Date -->
                <div class="col-md-3">
                    <label class="form-label fw-bold" for="expense_date">Expense Date <span class="text-danger">*</span></label>
                    <input class="form-control @error('expense_date') is-invalid @enderror" id="expense_date" type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required />
                    @error('expense_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Expense Category -->
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="expense_category_id">Expense Category <span class="text-danger">*</span></label>
                    <select class="form-select @error('expense_category_id') is-invalid @enderror" id="expense_category_id" name="expense_category_id" required>
                        <option value="">-- Choose Category --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('expense_category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Booking Link (Optional) -->
                <div class="col-md-5">
                    <label class="form-label fw-bold" for="booking_id">Link to Booking (Optional)</label>
                    <select class="form-select @error('booking_id') is-invalid @enderror" id="booking_id" name="booking_id">
                        <option value="">-- General Business Expense (No specific booking) --</option>
                        @foreach($bookings as $b)
                        <option value="{{ $b->id }}" {{ (old('booking_id', $selectedBooking->id ?? '') == $b->id) ? 'selected' : '' }}>
                            {{ $b->booking_number }} - {{ $b->customer->name }} (Event: {{ $b->event_date->format('d M Y') }})
                        </option>
                        @endforeach
                    </select>
                    <div class="fs-11 text-500">Associate with an event to calculate booking-level profit</div>
                </div>

                <!-- Amount -->
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="amount">Expense Amount ({{ $globalSettings->currency_symbol ?? 'Rs.' }}) <span class="text-danger">*</span></label>
                    <input class="form-control @error('amount') is-invalid @enderror" id="amount" type="number" step="0.01" name="amount" value="{{ old('amount') }}" placeholder="0.00" required />
                    @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="payment_method">Paid Via <span class="text-danger">*</span></label>
                    <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash (Petty Cash)</option>
                        <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Account</option>
                        <option value="online_transfer" {{ old('payment_method') == 'online_transfer' ? 'selected' : '' }}>Online Transfer (Raast / JazzCash / Easypaisa)</option>
                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Receipt Attachment -->
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="attachment">Bill / Voucher Attachment</label>
                    <input class="form-control @error('attachment') is-invalid @enderror" id="attachment" type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" />
                    <div class="fs-11 text-500">Scan or photo of vendor receipt (Max 5MB)</div>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label class="form-label fw-bold" for="description">Expense Description / Purpose <span class="text-danger">*</span></label>
                    <input class="form-control @error('description') is-invalid @enderror" id="description" type="text" name="description" value="{{ old('description') }}" placeholder="e.g. 50 bundles fresh white roses from flower mandi, 4 labour wages for tent setup..." required />
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remarks -->
                <div class="col-12">
                    <label class="form-label fw-bold" for="remarks">Remarks / Vendor Details</label>
                    <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="2" placeholder="Vendor name, shop receipt #, phone number...">{{ old('remarks') }}</textarea>
                </div>

                <div class="col-12 text-end mt-4">
                    <button class="btn btn-danger px-5 py-2 fs-10 fw-bold" type="submit">
                        <span class="fas fa-check-circle me-1"></span> Save Expense
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
