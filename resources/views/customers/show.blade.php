@extends('layouts.app')

@section('title', 'Customer Details: ' . $customer->name)

@section('content')
<!-- Header Card -->
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-sm-auto mb-2 mb-sm-0">
                <h5 class="fs-9 mb-0 text-900">
                    <span class="fas fa-user-circle text-primary me-2"></span>{{ $customer->name }}
                </h5>
                <div class="fs-11 text-500">Customer Profile &amp; Complete Event Financial History</div>
            </div>
            <div class="col-sm-auto">
                <a class="btn btn-primary btn-sm me-1" href="{{ route('bookings.create', ['customer_id' => $customer->id]) }}">
                    <span class="fas fa-calendar-plus me-1"></span> New Booking
                </a>
                <a class="btn btn-falcon-default btn-sm me-1" href="{{ route('customers.edit', $customer) }}">
                    <span class="fas fa-edit me-1"></span> Edit Customer
                </a>
                <a class="btn btn-falcon-default btn-sm" href="{{ route('customers.index') }}">
                    <span class="fas fa-arrow-left me-1"></span> Back
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="fs-11 text-uppercase text-500 fw-bold">Primary Mobile</div>
                <div class="fs-9 fw-bold text-primary">{{ $customer->mobile_number }}</div>
            </div>
            <div class="col-md-3">
                <div class="fs-11 text-uppercase text-500 fw-bold">Alternate Contact</div>
                <div class="fs-10 fw-semi-bold">{{ $customer->alternate_mobile ?? 'None' }}</div>
            </div>
            <div class="col-md-4">
                <div class="fs-11 text-uppercase text-500 fw-bold">Address</div>
                <div class="fs-10 text-700">{{ $customer->address ?? 'Not provided' }}</div>
            </div>
            <div class="col-md-2">
                <div class="fs-11 text-uppercase text-500 fw-bold">Status</div>
                @if($customer->is_active)
                <span class="badge badge-subtle-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                @else
                <span class="badge badge-subtle-danger"><i class="fas fa-ban me-1"></i> Inactive</span>
                @endif
            </div>
            @if($customer->notes)
            <div class="col-12 mt-2">
                <div class="fs-11 text-uppercase text-500 fw-bold">Notes</div>
                <div class="fs-11 text-600 bg-light p-2 rounded">{{ $customer->notes }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Financial Summary KPI Cards -->
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-md-3">
        <div class="card h-100 border-start border-4 border-primary">
            <div class="card-body p-3">
                <div class="fs-11 text-uppercase text-700 fw-semi-bold">Total Bookings</div>
                <h3 class="fw-bolder mb-0 text-primary">{{ $customer->bookings->count() }}</h3>
                <div class="fs-11 text-500 mt-1">Lifetime bookings placed</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card h-100 border-start border-4 border-info">
            <div class="card-body p-3">
                <div class="fs-11 text-uppercase text-700 fw-semi-bold">Total Billed</div>
                <h3 class="fw-bolder mb-0 text-900">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($customer->total_booking_amount, 0) }}</h3>
                <div class="fs-11 text-500 mt-1">Excludes cancelled events</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card h-100 border-start border-4 border-success">
            <div class="card-body p-3">
                <div class="fs-11 text-uppercase text-700 fw-semi-bold">Payments Received</div>
                <h3 class="fw-bolder mb-0 text-success">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($customer->total_paid_amount, 0) }}</h3>
                <div class="fs-11 text-500 mt-1">Net non-refunded cash</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card h-100 border-start border-4 border-danger">
            <div class="card-body p-3">
                <div class="fs-11 text-uppercase text-700 fw-semi-bold">Outstanding Balance</div>
                <h3 class="fw-bolder mb-0 {{ $customer->outstanding_balance > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($customer->outstanding_balance, 0) }}
                </h3>
                <div class="fs-11 text-500 mt-1">Current customer payable</div>
            </div>
        </div>
    </div>
</div>

<!-- Booking History Table -->
<div class="card mb-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0 fs-10 text-900 fw-bold">
            <span class="fas fa-calendar-check text-primary me-2"></span>Booking History
        </h6>
        <span class="badge badge-subtle-secondary">{{ $customer->bookings->count() }} Event(s)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Booking #</th>
                        <th>Event Date</th>
                        <th>Event Type</th>
                        <th>Stage Category</th>
                        <th>Type</th>
                        <th class="text-end">Final Amount</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->bookings as $b)
                    <tr>
                        <td class="fw-bold">
                            <a href="{{ route('bookings.show', $b) }}" class="text-primary">{{ $b->booking_number }}</a>
                        </td>
                        <td>
                            <span class="fw-semi-bold">{{ $b->event_date->format('d M Y') }}</span>
                            <div class="fs-11 text-500">{{ $b->event_start_time ? date('h:i A', strtotime($b->event_start_time)) : '' }}</div>
                        </td>
                        <td>{{ $b->eventType->name ?? 'N/A' }}</td>
                        <td>{{ $b->decorationCategory->name ?? 'Custom' }}</td>
                        <td>
                            <span class="badge badge-subtle-{{ $b->booking_type === 'indoor' ? 'info' : 'warning' }}">
                                {{ ucfirst($b->booking_type) }}
                            </span>
                        </td>
                        <td class="text-end fw-bold">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($b->final_amount, 0) }}</td>
                        <td class="text-end text-success fw-semi-bold">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($b->total_paid, 0) }}</td>
                        <td class="text-end {{ $b->remaining_balance > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($b->remaining_balance, 0) }}
                        </td>
                        <td>
                            @php
                                $bClass = match($b->status) {
                                    'confirmed' => 'success',
                                    'tentative' => 'info',
                                    'completed' => 'primary',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge badge-subtle-{{ $bClass }}">{{ ucfirst($b->status) }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('bookings.show', $b) }}" class="btn btn-falcon-default" title="View Booking">
                                    <span class="fas fa-eye"></span>
                                </a>
                                @if($b->remaining_balance > 0 && $b->status !== 'cancelled')
                                <a href="{{ route('payments.create', ['booking_id' => $b->id]) }}" class="btn btn-falcon-success" title="Collect Payment">
                                    <span class="fas fa-hand-holding-usd"></span>
                                </a>
                                @endif
                                <a href="{{ route('bookings.confirmation-slip', $b) }}" class="btn btn-falcon-default" title="Print Slip" target="_blank">
                                    <span class="fas fa-print"></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-500">No bookings on record for this customer.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment History Table -->
<div class="card mb-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0 fs-10 text-900 fw-bold">
            <span class="fas fa-receipt text-success me-2"></span>Payment Transactions Ledger
        </h6>
        <span class="badge badge-subtle-success">{{ $customer->payments->count() }} Payment(s)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Receipt #</th>
                        <th>Payment Date</th>
                        <th>Booking #</th>
                        <th>Payment Type</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Print</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer->payments as $p)
                    <tr>
                        <td class="fw-bold">{{ $p->receipt_number }}</td>
                        <td>{{ $p->payment_date->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('bookings.show', $p->booking_id) }}" class="text-primary">
                                {{ $p->booking->booking_number ?? 'Booking #' . $p->booking_id }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-subtle-{{ $p->is_refund ? 'danger' : 'success' }}">
                                {{ ucfirst($p->payment_type) }}
                            </span>
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $p->payment_method)) }}</td>
                        <td>{{ $p->transaction_reference ?? '—' }}</td>
                        <td class="text-end fw-bold {{ $p->is_refund ? 'text-danger' : 'text-success' }}">
                            {{ $p->is_refund ? '-' : '+' }} {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($p->amount, 0) }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('payments.receipt', $p) }}" class="btn btn-falcon-default btn-sm" target="_blank" title="Print Receipt">
                                <span class="fas fa-print"></span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-500">No payment transactions recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
