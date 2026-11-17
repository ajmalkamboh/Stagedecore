@extends('layouts.app')

@section('title', 'Booking Details: ' . $booking->booking_number)

@section('content')
<!-- Top Header & Actions -->
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-sm-auto mb-2 mb-sm-0">
                <div class="d-flex align-items-center">
                    <h5 class="fs-9 mb-0 text-900 me-2">
                        <span class="fas fa-bookmark text-primary me-2"></span>Booking: {{ $booking->booking_number }}
                    </h5>
                    @php
                        $bClass = match($booking->status) {
                            'confirmed' => 'success',
                            'tentative' => 'info',
                            'completed' => 'primary',
                            'cancelled' => 'danger',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge badge-subtle-{{ $bClass }} fs-10 px-2 py-1 text-uppercase">{{ $booking->status }}</span>
                </div>
                <div class="fs-11 text-500 mt-1">Booked on {{ $booking->booking_date->format('d M, Y') }} by {{ $booking->creator->name ?? 'Staff' }}</div>
            </div>
            <div class="col-sm-auto d-flex flex-wrap gap-1">
                @if($booking->remaining_balance > 0 && $booking->status !== 'cancelled')
                <a class="btn btn-success btn-sm" href="{{ route('payments.create', ['booking_id' => $booking->id]) }}">
                    <span class="fas fa-hand-holding-usd me-1"></span> Add Payment
                </a>
                @endif
                <a class="btn btn-falcon-default btn-sm" href="{{ route('expenses.create', ['booking_id' => $booking->id]) }}">
                    <span class="fas fa-receipt text-danger me-1"></span> Add Booking Expense
                </a>
                <a class="btn btn-falcon-default btn-sm" href="{{ route('bookings.edit', $booking) }}">
                    <span class="fas fa-edit me-1"></span> Edit
                </a>
                <a class="btn btn-primary btn-sm" href="{{ route('bookings.confirmation-slip', $booking) }}" target="_blank">
                    <span class="fas fa-print me-1"></span> Print Slip
                </a>
                <button type="button" class="btn btn-falcon-default btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal">
                    <span class="fas fa-tasks me-1"></span> Change Status
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Resource Overlap Warning (if any) -->
@if($overlapWarning)
<div class="alert alert-warning border-2 d-flex align-items-center mb-3" role="alert">
    <div class="bg-warning me-3 icon-item text-white rounded-circle"><span class="fas fa-exclamation-triangle"></span></div>
    <div class="flex-1">
        <h6 class="fw-bold mb-0 text-900">Resource Overlap Advisory</h6>
        <div class="fs-11 text-800">{{ $overlapWarning }}</div>
    </div>
</div>
@endif

<!-- Cancellation Notice (if cancelled) -->
@if($booking->status === 'cancelled')
<div class="alert alert-danger border-2 mb-3" role="alert">
    <div class="d-flex align-items-center mb-1">
        <span class="fas fa-ban me-2 fs-3"></span>
        <h6 class="mb-0 fw-bold">This Booking was Cancelled on {{ $booking->cancelled_at ? $booking->cancelled_at->format('d M Y, h:i A') : 'N/A' }}</h6>
    </div>
    <p class="mb-0 fs-11"><strong>Reason:</strong> {{ $booking->cancellation_reason ?? 'No specific reason provided.' }}</p>
</div>
@endif

<!-- Information Row -->
<div class="row g-3 mb-3">
    <!-- Customer & Event Schedule -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fs-10 fw-bold text-900"><span class="fas fa-info-circle text-primary me-2"></span>Event &amp; Venue Details</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="fs-11 text-uppercase text-500 fw-bold">Customer Information</div>
                        <div class="fs-9 fw-bold text-primary mt-1">
                            <a href="{{ route('customers.show', $booking->customer_id) }}">{{ $booking->customer->name }}</a>
                        </div>
                        <div class="fs-10 text-700 mt-1"><i class="fas fa-phone-alt me-1 text-400"></i> {{ $booking->customer->mobile_number }}</div>
                        @if($booking->customer->alternate_mobile)
                        <div class="fs-11 text-500"><i class="fab fa-whatsapp me-1 text-success"></i> {{ $booking->customer->alternate_mobile }}</div>
                        @endif
                    </div>

                    <div class="col-sm-6">
                        <div class="fs-11 text-uppercase text-500 fw-bold">Event Schedule</div>
                        <div class="fs-9 fw-bold text-900 mt-1">
                            {{ $booking->event_date->format('l, d F Y') }}
                        </div>
                        <div class="fs-10 text-600 mt-1">
                            <i class="far fa-clock me-1 text-400"></i>
                            @if($booking->event_start_time)
                                {{ date('h:i A', strtotime($booking->event_start_time)) }}
                                @if($booking->event_end_time)
                                    — {{ date('h:i A', strtotime($booking->event_end_time)) }}
                                @endif
                            @else
                                Full Day Event
                            @endif
                        </div>
                    </div>

                    <div class="col-12"><hr class="my-1"></div>

                    <div class="col-sm-6">
                        <div class="fs-11 text-uppercase text-500 fw-bold">Event Type &amp; Venue Type</div>
                        <div class="mt-1">
                            <span class="badge badge-subtle-primary fs-11 me-1">{{ $booking->eventType->name ?? 'Event' }}</span>
                            <span class="badge badge-subtle-{{ $booking->booking_type === 'indoor' ? 'info' : 'warning' }} fs-11">
                                {{ ucfirst($booking->booking_type) }} Setup
                            </span>
                        </div>
                        <div class="mt-2">
                            <div class="fs-11 text-uppercase text-500 fw-bold">Venue Name</div>
                            <div class="fs-10 fw-semi-bold text-800">{{ $booking->venue ? $booking->venue->name : 'Custom / Unregistered Venue' }}</div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="fs-11 text-uppercase text-500 fw-bold">Venue Address / Location</div>
                        <div class="fs-10 text-700 mt-1 bg-light p-2 rounded">
                            {{ $booking->venue_address ?? ($booking->venue->address ?? 'Address not specified') }}
                        </div>
                    </div>

                    <div class="col-12"><hr class="my-1"></div>

                    <!-- Stage Decoration Specs -->
                    <div class="col-12">
                        <div class="fs-11 text-uppercase text-500 fw-bold">Stage Decoration Category</div>
                        <div class="fs-9 fw-bold text-primary mt-1">
                            {{ $booking->decorationCategory->name ?? 'Custom Decoration Package' }}
                        </div>
                        @if($booking->decoration_details)
                        <div class="mt-2">
                            <div class="fs-11 text-uppercase text-500 fw-bold">Decoration Specifications</div>
                            <div class="fs-10 text-800 bg-light p-2 rounded mt-1" style="white-space: pre-line;">{{ $booking->decoration_details }}</div>
                        </div>
                        @endif

                        @if($booking->special_instructions)
                        <div class="mt-2">
                            <div class="fs-11 text-uppercase text-500 fw-bold">Special Instructions &amp; Setup Notes</div>
                            <div class="fs-10 text-muted fst-italic p-2 bg-light rounded mt-1" style="white-space: pre-line;">{{ $booking->special_instructions }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Breakdown & Profit Card -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fs-10 fw-bold text-900"><span class="fas fa-file-invoice-dollar text-success me-2"></span>Financial Summary</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-3">
                    <tbody>
                        <tr>
                            <td class="text-600">Total Booking Amount:</td>
                            <td class="text-end fw-semi-bold">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->total_amount, 2) }}</td>
                        </tr>
                        @if($booking->discount > 0)
                        <tr>
                            <td class="text-600">Discount / Concession:</td>
                            <td class="text-end text-danger fw-semi-bold">- {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="table-light">
                            <td class="fw-bold fs-10">Final Booking Amount:</td>
                            <td class="text-end fw-bolder fs-10 text-primary">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->final_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-600">Total Paid by Client:</td>
                            <td class="text-end text-success fw-bold">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->total_paid, 2) }}</td>
                        </tr>
                        <tr class="table-warning">
                            <td class="fw-bold text-danger">Remaining Balance:</td>
                            <td class="text-end fw-bolder fs-9 text-danger">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->remaining_balance, 2) }}</td>
                        </tr>
                        @if($booking->has_commission)
                        <tr>
                            <td class="text-600">
                                <span class="fas fa-hand-holding-usd text-warning me-1"></span> Venue Commission:
                            </td>
                            <td class="text-end fw-bold {{ $booking->commission_status === 'paid' ? 'text-success' : 'text-warning' }}">
                                {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->commission_amount, 2) }}
                                <span class="badge bg-{{ $booking->commission_status === 'paid' ? 'success' : 'warning' }}-subtle text-{{ $booking->commission_status === 'paid' ? 'success' : 'warning' }} fs-11 ms-1">
                                    {{ ucfirst($booking->commission_status) }}
                                </span>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                <!-- Booking-level Estimated Profit Calculation -->
                <div class="border rounded p-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fs-11 text-uppercase text-600 fw-bold">Direct Booking Expenses:</span>
                        <span class="fw-bold text-danger">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->total_expenses, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="fs-10 text-uppercase fw-bold text-900">Estimated Booking Profit:</span>
                        <span class="fs-8 fw-bolder {{ $booking->estimated_profit >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->estimated_profit, 2) }}
                        </span>
                    </div>
                    <div class="fs-11 text-500 mt-1">Calculated as (Final Amount - Direct Booking Expenses)</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payments Received Section -->
<div class="card mb-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0 fs-10 fw-bold text-900">
            <span class="fas fa-money-check-alt text-success me-2"></span>Customer Payments Received ({{ $booking->payments->count() }})
        </h6>
        @if($booking->remaining_balance > 0 && $booking->status !== 'cancelled')
        <a class="btn btn-success btn-sm py-1" href="{{ route('payments.create', ['booking_id' => $booking->id]) }}">
            <span class="fas fa-plus me-1"></span> Record Payment
        </a>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Receipt #</th>
                        <th>Payment Date</th>
                        <th>Payment Type</th>
                        <th>Method</th>
                        <th>Reference / Remarks</th>
                        <th>Received By</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($booking->payments as $payment)
                    <tr>
                        <td class="fw-bold">{{ $payment->receipt_number }}</td>
                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                        <td>
                            <span class="badge badge-subtle-{{ $payment->is_refund ? 'danger' : 'success' }}">
                                {{ ucfirst($payment->payment_type) }}
                            </span>
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td class="fs-11 text-600">
                            {{ $payment->transaction_reference ? 'Ref: ' . $payment->transaction_reference : '' }}
                            {{ $payment->remarks ? '(' . $payment->remarks . ')' : '' }}
                        </td>
                        <td class="fs-11">{{ $payment->receiver->name ?? 'Staff' }}</td>
                        <td class="text-end fw-bold {{ $payment->is_refund ? 'text-danger' : 'text-success' }}">
                            {{ $payment->is_refund ? '-' : '+' }} {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('payments.receipt', $payment) }}" class="btn btn-falcon-default btn-sm py-0 px-2" target="_blank" title="Print Receipt">
                                <span class="fas fa-print"></span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-500">
                            No payment transactions recorded for this booking yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Booking-Related Expenses Section -->
<div class="card mb-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0 fs-10 fw-bold text-900">
            <span class="fas fa-receipt text-danger me-2"></span>Direct Booking Expenses ({{ $booking->expenses->count() }})
        </h6>
        <a class="btn btn-danger btn-sm py-1" href="{{ route('expenses.create', ['booking_id' => $booking->id]) }}">
            <span class="fas fa-plus me-1"></span> Add Booking Expense
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Expense #</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Method</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($booking->expenses as $expense)
                    <tr>
                        <td class="fw-bold">{{ $expense->expense_number }}</td>
                        <td>{{ $expense->expense_date->format('d M Y') }}</td>
                        <td><span class="badge badge-subtle-primary">{{ $expense->category->name ?? 'Expense' }}</span></td>
                        <td>{{ $expense->description }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</td>
                        <td class="text-end fw-bold text-danger">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($expense->amount, 2) }}</td>
                        <td class="text-end">
                            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-falcon-default btn-sm py-0 px-2">
                                <span class="fas fa-edit"></span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-500">
                            No direct expenses recorded against this booking yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Venue Commission Section -->
@if($booking->has_commission)
<div class="card mb-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0 fs-10 fw-bold text-900">
            <span class="fas fa-hand-holding-usd text-warning me-2"></span>Venue / Marquee Referral Commission
        </h6>
        <div>
            @if($booking->commission_status === 'pending')
                <button type="button" class="btn btn-warning btn-sm py-1" data-bs-toggle="modal" data-bs-target="#payBookingCommissionModal">
                    <span class="fas fa-hand-holding-usd me-1"></span> Pay Commission
                </button>
            @else
                <a href="{{ route('commissions.voucher', $booking) }}" target="_blank" class="btn btn-falcon-default btn-sm py-1">
                    <span class="fas fa-print me-1"></span> Print Voucher
                </a>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row align-items-center g-3">
            <div class="col-md-3">
                <span class="fs-11 text-muted text-uppercase fw-bold d-block">Venue Partner:</span>
                <span class="fs-10 fw-bold text-dark">{{ $booking->venue?->name ?? 'Custom Venue' }}</span>
                <div class="fs-11 text-muted">{{ $booking->venue?->venue_type ?? 'Event Venue' }}</div>
            </div>
            <div class="col-md-3">
                <span class="fs-11 text-muted text-uppercase fw-bold d-block">Commission Payable:</span>
                <span class="fs-9 fw-bold text-danger">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->commission_amount, 2) }}</span>
                <div class="fs-11 text-muted">
                    @if($booking->commission_type === 'percentage')
                        {{ number_format($booking->commission_rate, 1) }}% of Final Amount
                    @else
                        Fixed Cash Amount
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <span class="fs-11 text-muted text-uppercase fw-bold d-block">Commission Status:</span>
                @if($booking->commission_status === 'paid')
                    <span class="badge bg-success-subtle text-success fs-10"><span class="fas fa-check-circle me-1"></span>Paid</span>
                    <div class="fs-11 text-muted">Paid on {{ $booking->commission_paid_at?->format('d M, Y') }} via {{ strtoupper(str_replace('_', ' ', $booking->commission_payment_method)) }}</div>
                @else
                    <span class="badge bg-warning-subtle text-warning fs-10"><span class="fas fa-hourglass-half me-1"></span>Pending (Unpaid)</span>
                @endif
            </div>
            <div class="col-md-3">
                <span class="fs-11 text-muted text-uppercase fw-bold d-block">Recipient / Notes:</span>
                <span class="fs-10 fw-bold text-800">{{ $booking->commission_recipient ?? 'Venue Management' }}</span>
                @if($booking->commission_notes)
                <div class="fs-11 text-muted">{{ $booking->commission_notes }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($booking->commission_status === 'pending')
<!-- Modal: Pay Commission for Booking -->
<div class="modal fade" id="payBookingCommissionModal" tabindex="-1" aria-labelledby="payBookingCommissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('commissions.pay', $booking) }}">
                @csrf
                <div class="modal-header bg-warning-subtle">
                    <h5 class="modal-title fs-9 fw-bold text-dark" id="payBookingCommissionModalLabel">
                        <span class="fas fa-hand-holding-usd me-2 text-warning"></span>Pay Venue Commission
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 fs-10 mb-3">
                        Recording this payment will automatically log an expense under <strong>"Venue Commission / Referral Fee"</strong>.
                    </div>

                    <div class="mb-2">
                        <span class="fw-bold">Booking:</span> <span class="text-primary fw-bold">{{ $booking->booking_number }}</span> &bull; 
                        <span class="fw-bold">Venue:</span> <span class="text-800">{{ $booking->venue?->name ?? 'Custom Venue' }}</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="b_paid_amount">Payout Amount ({{ $globalSettings->currency_symbol ?? 'Rs.' }}) <span class="text-danger">*</span></label>
                            <input class="form-control" type="number" step="0.01" min="0.01" id="b_paid_amount" name="paid_amount" value="{{ $booking->commission_amount }}" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="b_paid_date">Date Paid <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" id="b_paid_date" name="paid_date" value="{{ date('Y-m-d') }}" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="b_payment_method">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="b_payment_method" name="payment_method" required>
                                <option value="cash" selected>Cash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="online_transfer">Online (Raast / JazzCash / Easypaisa)</option>
                                <option value="cheque">Cheque</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="b_recipient">Paid To (Recipient) <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" id="b_recipient" name="recipient" value="{{ $booking->commission_recipient ?? $booking->venue?->name }}" required />
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" for="b_notes">Remarks / Transaction Details</label>
                            <textarea class="form-control" id="b_notes" name="notes" rows="2" placeholder="e.g. Paid in cash at venue reception">{{ $booking->commission_notes }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <span class="fas fa-check me-1"></span> Confirm &amp; Save Payout
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endif

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('bookings.update-status', $booking) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header bg-light">
                    <h5 class="modal-title fs-9" id="statusModalLabel">Change Booking Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold" for="modal_status">Status</label>
                        <select class="form-select" id="modal_status" name="status" required>
                            <option value="tentative" {{ $booking->status == 'tentative' ? 'selected' : '' }}>Tentative</option>
                            <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3" id="modal_cancel_reason_box" style="display: {{ $booking->status === 'cancelled' ? 'block' : 'none' }};">
                        <label class="form-label fw-bold text-danger" for="modal_cancellation_reason">Cancellation Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="modal_cancellation_reason" name="cancellation_reason" rows="3" placeholder="Provide reason for cancelling this booking...">{{ $booking->cancellation_reason }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalStatus = document.getElementById('modal_status');
    const modalCancelBox = document.getElementById('modal_cancel_reason_box');
    const modalCancelInput = document.getElementById('modal_cancellation_reason');

    if (modalStatus && modalCancelBox) {
        modalStatus.addEventListener('change', function() {
            if (this.value === 'cancelled') {
                modalCancelBox.style.display = 'block';
                modalCancelInput.setAttribute('required', 'required');
            } else {
                modalCancelBox.style.display = 'none';
                modalCancelInput.removeAttribute('required');
            }
        });
    }
});
</script>
@endpush
