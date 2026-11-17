@extends('layouts.app')

@section('title', 'Venue Commission Statement & Reports')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-auto">
                <h5 class="fs-9 mb-0">
                    <span class="fas fa-chart-pie text-success me-2"></span>Venue Commission Ledger &amp; Statements
                </h5>
                <p class="fs-11 text-600 mb-0">Venue-wise breakdown of commissions payable, payments settled, and outstanding balances.</p>
            </div>
            <div class="col-auto">
                <a class="btn btn-falcon-default btn-sm me-2" href="{{ route('commissions.index') }}">
                    <span class="fas fa-list me-1"></span> Commission List
                </a>
                <button onclick="window.print()" class="btn btn-falcon-primary btn-sm">
                    <span class="fas fa-print me-1"></span> Print Statement
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-md-3">
        <div class="card h-100 border-start border-4 border-info">
            <div class="card-body">
                <h6 class="text-uppercase text-600 fs-11 mb-1">Total Venue Booking Sales</h6>
                <div class="fs-7 fw-bold text-info">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($grandTotalRevenue, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card h-100 border-start border-4 border-primary">
            <div class="card-body">
                <h6 class="text-uppercase text-600 fs-11 mb-1">Total Commission Payable</h6>
                <div class="fs-7 fw-bold text-primary">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($grandTotalCommission, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card h-100 border-start border-4 border-success">
            <div class="card-body">
                <h6 class="text-uppercase text-600 fs-11 mb-1">Total Commission Paid</h6>
                <div class="fs-7 fw-bold text-success">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($grandTotalPaid, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-3">
        <div class="card h-100 border-start border-4 border-danger">
            <div class="card-body">
                <h6 class="text-uppercase text-600 fs-11 mb-1">Outstanding Pending Commission</h6>
                <div class="fs-7 fw-bold text-danger">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($grandTotalPending, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="card mb-3 no-print">
    <div class="card-body bg-light">
        <form method="GET" action="{{ route('commissions.report') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select class="form-select form-select-sm" name="venue_id">
                    <option value="">-- All Venues / Marquees --</option>
                    @foreach($venuesList as $v)
                    <option value="{{ $v->id }}" {{ $venueId == $v->id ? 'selected' : '' }}>{{ $v->name }} ({{ $v->venue_type }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input class="form-control form-control-sm" type="date" name="date_from" value="{{ $dateFrom }}" title="Date From" />
            </div>
            <div class="col-md-3">
                <input class="form-control form-control-sm" type="date" name="date_to" value="{{ $dateTo }}" title="Date To" />
            </div>
            <div class="col-md-2 text-end">
                <button class="btn btn-primary btn-sm px-3 w-100" type="submit">
                    <span class="fas fa-filter me-1"></span> Filter Report
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Venue-wise Ledger Summary -->
<div class="card mb-3">
    <div class="card-header bg-light">
        <h6 class="mb-0 fs-10 fw-bold text-800">Venue Commission Summary by Partner Venue</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped fs-10 mb-0">
                <thead class="bg-200 text-900">
                    <tr>
                        <th class="py-2">Venue / Marquee Name</th>
                        <th class="py-2">Type</th>
                        <th class="py-2">Contact</th>
                        <th class="py-2 text-center">Events</th>
                        <th class="py-2 text-end">Total Booking Revenue</th>
                        <th class="py-2 text-end">Commission Payable</th>
                        <th class="py-2 text-end">Commission Paid</th>
                        <th class="py-2 text-end">Pending Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($venueStats as $v)
                    <tr class="align-middle">
                        <td class="fw-bold text-dark">{{ $v->name }}</td>
                        <td>{{ $v->venue_type }}</td>
                        <td>{{ $v->contact_number ?? 'N/A' }}</td>
                        <td class="text-center fw-bold">{{ $v->total_events }}</td>
                        <td class="text-end fw-semibold">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($v->total_revenue, 2) }}
                        </td>
                        <td class="text-end fw-bold text-primary">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($v->total_commission, 2) }}
                        </td>
                        <td class="text-end fw-bold text-success">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($v->paid_commission, 2) }}
                        </td>
                        <td class="text-end fw-bold {{ $v->pending_commission > 0 ? 'text-danger' : 'text-muted' }}">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($v->pending_commission, 2) }}
                        </td>
                    </tr>

                    @if($v->bookings->isNotEmpty())
                    <!-- Detailed Event Breakdown Sub-rows -->
                    <tr class="bg-body-tertiary">
                        <td colspan="8" class="p-2">
                            <div class="table-responsive border rounded bg-white p-2">
                                <div class="fs-11 fw-bold text-700 mb-1">Events &amp; Commission History for {{ $v->name }}:</div>
                                <table class="table table-sm table-borderless fs-11 mb-0">
                                    <thead>
                                        <tr class="text-muted border-bottom">
                                            <th>Booking #</th>
                                            <th>Event Date</th>
                                            <th>Customer</th>
                                            <th class="text-end">Final Amount</th>
                                            <th class="text-center">Rate</th>
                                            <th class="text-end">Commission</th>
                                            <th>Recipient</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($v->bookings as $b)
                                        <tr>
                                            <td class="fw-bold"><a href="{{ route('bookings.show', $b) }}">{{ $b->booking_number }}</a></td>
                                            <td>{{ $b->event_date->format('d M, Y') }}</td>
                                            <td>{{ $b->customer?->name }}</td>
                                            <td class="text-end">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($b->final_amount, 2) }}</td>
                                            <td class="text-center">
                                                {{ $b->commission_type === 'percentage' ? number_format($b->commission_rate, 1) . '%' : 'Fixed' }}
                                            </td>
                                            <td class="text-end fw-bold text-danger">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($b->commission_amount, 2) }}</td>
                                            <td>{{ $b->commission_recipient ?? 'Venue' }}</td>
                                            <td class="text-center">
                                                @if($b->commission_status === 'paid')
                                                    <span class="badge bg-success-subtle text-success">Paid ({{ $b->commission_paid_at?->format('d M') }})</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                    @endif

                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <span class="fas fa-info-circle me-1"></span> No venue commission history found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-200 text-900 fw-bold">
                    <tr>
                        <td colspan="4" class="text-end py-2">Grand Totals:</td>
                        <td class="text-end py-2">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($grandTotalRevenue, 2) }}</td>
                        <td class="text-end py-2 text-primary">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($grandTotalCommission, 2) }}</td>
                        <td class="text-end py-2 text-success">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($grandTotalPaid, 2) }}</td>
                        <td class="text-end py-2 text-danger">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($grandTotalPending, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
