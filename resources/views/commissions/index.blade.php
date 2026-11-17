@extends('layouts.app')

@section('title', 'Venue Commission & Referral Management')

@section('content')
<!-- Page Header -->
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-auto">
                <h5 class="fs-9 mb-0">
                    <span class="fas fa-hand-holding-usd text-warning me-2"></span>Venue &amp; Marquee Commissions
                </h5>
                <p class="fs-11 text-600 mb-0">Track referral fees and commissions payable to marriage halls, marquees, hotels, and farmhouses.</p>
            </div>
            <div class="col-auto">
                <a class="btn btn-falcon-success btn-sm me-2" href="{{ route('commissions.report') }}">
                    <span class="fas fa-chart-pie me-1"></span> Venue Commission Statements
                </a>
                <a class="btn btn-falcon-default btn-sm" href="{{ route('bookings.create') }}">
                    <span class="fas fa-plus me-1"></span> New Booking
                </a>
            </div>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-md-3">
        <div class="card h-100 border-start border-4 border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <h6 class="text-uppercase text-600 fs-11 mb-1">Total Commission Accrued</h6>
                        <div class="fs-7 fw-bold text-primary">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($totalCommission, 2) }}</div>
                    </div>
                    <div class="avatar avatar-l bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                        <span class="fas fa-calculator fs-9"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card h-100 border-start border-4 border-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <h6 class="text-uppercase text-600 fs-11 mb-1">Commission Paid</h6>
                        <div class="fs-7 fw-bold text-success">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($paidCommission, 2) }}</div>
                    </div>
                    <div class="avatar avatar-l bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center">
                        <span class="fas fa-check-circle fs-9"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card h-100 border-start border-4 border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <h6 class="text-uppercase text-600 fs-11 mb-1">Pending Unpaid Balance</h6>
                        <div class="fs-7 fw-bold text-warning">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($pendingCommission, 2) }}</div>
                    </div>
                    <div class="avatar avatar-l bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center">
                        <span class="fas fa-hourglass-half fs-9"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card h-100 border-start border-4 border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <h6 class="text-uppercase text-600 fs-11 mb-1">Venues With Pending Dues</h6>
                        <div class="fs-7 fw-bold text-info">{{ $venuesWithPending }} Venues</div>
                    </div>
                    <div class="avatar avatar-l bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center">
                        <span class="fas fa-hotel fs-9"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="card mb-3">
    <div class="card-body bg-light">
        <form method="GET" action="{{ route('commissions.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input class="form-control form-control-sm" type="text" name="search" value="{{ $search }}" placeholder="Search booking #, recipient, venue, client..." />
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" name="venue_id">
                    <option value="">-- All Venues / Marquees --</option>
                    @foreach($venues as $v)
                    <option value="{{ $v->id }}" {{ $venueId == $v->id ? 'selected' : '' }}>{{ $v->name }} ({{ $v->venue_type }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select form-select-sm" name="status">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending (Unpaid)</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="col-md-2">
                <input class="form-control form-control-sm" type="date" name="date_from" value="{{ $dateFrom }}" title="Event Date From" />
            </div>
            <div class="col-md-2">
                <input class="form-control form-control-sm" type="date" name="date_to" value="{{ $dateTo }}" title="Event Date To" />
            </div>
            <div class="col-12 text-end">
                <a class="btn btn-link btn-sm text-secondary me-2" href="{{ route('commissions.index') }}">Reset Filters</a>
                <button class="btn btn-primary btn-sm px-3" type="submit">
                    <span class="fas fa-filter me-1"></span> Filter Records
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Commissions Data Table -->
<div class="card mb-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped fs-10 mb-0">
                <thead class="bg-200 text-900">
                    <tr>
                        <th class="py-2">Booking #</th>
                        <th class="py-2">Event Date</th>
                        <th class="py-2">Venue / Hall</th>
                        <th class="py-2">Customer</th>
                        <th class="py-2 text-end">Booking Final</th>
                        <th class="py-2 text-center">Rate / Terms</th>
                        <th class="py-2 text-end">Commission Payable</th>
                        <th class="py-2">Payable Recipient</th>
                        <th class="py-2 text-center">Status</th>
                        <th class="py-2 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="list">
                    @forelse($commissions as $comm)
                    <tr class="align-middle">
                        <td class="fw-bold">
                            <a href="{{ route('bookings.show', $comm) }}">{{ $comm->booking_number }}</a>
                        </td>
                        <td>{{ $comm->event_date->format('d M, Y') }}</td>
                        <td>
                            <div class="fw-bold text-800">{{ $comm->venue?->name ?? 'Custom Venue' }}</div>
                            <small class="text-muted">{{ $comm->venue?->venue_type ?? 'Event Venue' }}</small>
                        </td>
                        <td>{{ $comm->customer->name ?? 'N/A' }}</td>
                        <td class="text-end fw-semibold">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($comm->final_amount, 2) }}
                        </td>
                        <td class="text-center">
                            @if($comm->commission_type === 'percentage')
                                <span class="badge bg-secondary-subtle text-secondary">{{ number_format($comm->commission_rate, 1) }}% of Sales</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">Fixed Cash</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-danger">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($comm->commission_amount, 2) }}
                        </td>
                        <td>
                            <div class="fw-bold">{{ $comm->commission_recipient ?? ($comm->venue?->contact_number ? 'Venue Management' : 'N/A') }}</div>
                            @if($comm->commission_notes)
                            <small class="text-muted d-block text-truncate" style="max-width: 180px;" title="{{ $comm->commission_notes }}">{{ $comm->commission_notes }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($comm->commission_status === 'paid')
                                <span class="badge bg-success-subtle text-success fs-11"><span class="fas fa-check-circle me-1"></span>Paid</span>
                                <div class="fs-11 text-muted">{{ $comm->commission_paid_at?->format('d M, Y') }}</div>
                            @else
                                <span class="badge bg-warning-subtle text-warning fs-11"><span class="fas fa-clock me-1"></span>Pending</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                @if($comm->commission_status === 'pending')
                                <button type="button" class="btn btn-warning btn-sm btn-pay-commission"
                                    data-booking-id="{{ $comm->id }}"
                                    data-booking-number="{{ $comm->booking_number }}"
                                    data-venue-name="{{ $comm->venue?->name ?? 'Custom Venue' }}"
                                    data-amount="{{ $comm->commission_amount }}"
                                    data-recipient="{{ $comm->commission_recipient ?? $comm->venue?->name }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#payCommissionModal">
                                    <span class="fas fa-hand-holding-usd me-1"></span> Pay
                                </button>
                                @else
                                <a href="{{ route('commissions.voucher', $comm) }}" target="_blank" class="btn btn-falcon-default btn-sm" title="Print Official Voucher">
                                    <span class="fas fa-print me-1"></span> Voucher
                                </a>
                                @endif
                                <a href="{{ route('bookings.show', $comm) }}" class="btn btn-falcon-default btn-sm" title="View Booking">
                                    <span class="fas fa-eye"></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <span class="fas fa-info-circle me-1"></span> No venue commission records found matching the criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($commissions->hasPages())
    <div class="card-footer bg-light py-2">
        {{ $commissions->links() }}
    </div>
    @endif
</div>

<!-- Modal: Record Commission Payout -->
<div class="modal fade" id="payCommissionModal" tabindex="-1" aria-labelledby="payCommissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="payCommissionForm" method="POST" action="">
                @csrf
                <div class="modal-header bg-warning-subtle">
                    <h5 class="modal-title fs-9 fw-bold text-dark" id="payCommissionModalLabel">
                        <span class="fas fa-hand-holding-usd me-2 text-warning"></span>Record Venue Commission Payout
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 fs-10 mb-3">
                        Paying commission automatically records a verified expense transaction under <strong>"Venue Commission / Referral Fee"</strong> so profit and loss statements stay 100% accurate.
                    </div>

                    <div class="mb-2">
                        <span class="fw-bold">Booking:</span> <span id="modal_booking_number" class="text-primary fw-bold"></span> &bull; 
                        <span class="fw-bold">Venue:</span> <span id="modal_venue_name" class="text-800"></span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="paid_amount">Payout Amount ({{ $globalSettings->currency_symbol ?? 'Rs.' }}) <span class="text-danger">*</span></label>
                            <input class="form-control" type="number" step="0.01" min="0.01" id="modal_paid_amount" name="paid_amount" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="paid_date">Date Paid <span class="text-danger">*</span></label>
                            <input class="form-control" type="date" id="modal_paid_date" name="paid_date" value="{{ date('Y-m-d') }}" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="payment_method">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_payment_method" name="payment_method" required>
                                <option value="cash" selected>Cash</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="online_transfer">Online (Raast / JazzCash / Easypaisa)</option>
                                <option value="cheque">Cheque</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" for="recipient">Paid To (Recipient) <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" id="modal_recipient" name="recipient" required placeholder="Manager Aslam / Hall Cashier" />
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" for="notes">Remarks / Transaction Details</label>
                            <textarea class="form-control" id="modal_notes" name="notes" rows="2" placeholder="e.g. Paid in cash at reception after event setup check"></textarea>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payButtons = document.querySelectorAll('.btn-pay-commission');
    const form = document.getElementById('payCommissionForm');
    const bookingNumberSpan = document.getElementById('modal_booking_number');
    const venueNameSpan = document.getElementById('modal_venue_name');
    const amountInput = document.getElementById('modal_paid_amount');
    const recipientInput = document.getElementById('modal_recipient');

    payButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.getAttribute('data-booking-id');
            const bookingNumber = this.getAttribute('data-booking-number');
            const venueName = this.getAttribute('data-venue-name');
            const amount = this.getAttribute('data-amount');
            const recipient = this.getAttribute('data-recipient');

            form.action = "{{ url('/commissions') }}/" + bookingId + "/pay";
            bookingNumberSpan.textContent = bookingNumber;
            venueNameSpan.textContent = venueName;
            amountInput.value = amount;
            recipientInput.value = recipient || '';
        });
    });
});
</script>
@endpush
