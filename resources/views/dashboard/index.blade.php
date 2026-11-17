@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="card mb-3">
    <div class="card-body px-3 py-2 bg-light">
        <div class="row flex-between-center">
            <div class="col-sm-auto mb-2 mb-sm-0">
                <h5 class="fs-9 mb-0 text-900"><span class="fas fa-chart-line text-primary me-2"></span>Operations &amp; Financial Dashboard</h5>
                <div class="fs-11 text-500">Live operational figures &amp; real-time database statistics</div>
            </div>
            <div class="col-sm-auto">
                <a class="btn btn-primary btn-sm me-1" href="{{ route('bookings.create') }}">
                    <span class="fas fa-plus me-1"></span> New Booking
                </a>
                <a class="btn btn-falcon-default btn-sm" href="{{ route('calendar.index') }}">
                    <span class="fas fa-calendar-alt me-1"></span> View Calendar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Row 1: Operational Status Metric Cards -->
<div class="row g-3 mb-3">
    <!-- Total Bookings -->
    <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card h-100 border-start border-4 border-primary">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <h6 class="text-700 fs-11 text-uppercase mb-1">Total Bookings</h6>
                        <h3 class="fw-bolder mb-0 text-primary">{{ number_format($totalBookings) }}</h3>
                    </div>
                    <div class="avatar avatar-l bg-primary-subtle rounded-circle text-primary d-flex align-items-center justify-content-center">
                        <span class="fas fa-bookmark fs-10"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Events -->
    <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card h-100 border-start border-4 border-danger">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <h6 class="text-700 fs-11 text-uppercase mb-1">Today's Events</h6>
                        <h3 class="fw-bolder mb-0 text-danger">{{ number_format($todaysEventsCount) }}</h3>
                    </div>
                    <div class="avatar avatar-l bg-danger-subtle rounded-circle text-danger d-flex align-items-center justify-content-center">
                        <span class="fas fa-calendar-day fs-10"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmed Bookings -->
    <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card h-100 border-start border-4 border-success">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <h6 class="text-700 fs-11 text-uppercase mb-1">Confirmed</h6>
                        <h3 class="fw-bolder mb-0 text-success">{{ number_format($confirmedBookings) }}</h3>
                    </div>
                    <div class="avatar avatar-l bg-success-subtle rounded-circle text-success d-flex align-items-center justify-content-center">
                        <span class="fas fa-check-circle fs-10"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tentative Bookings -->
    <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card h-100 border-start border-4 border-info">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <h6 class="text-700 fs-11 text-uppercase mb-1">Tentative</h6>
                        <h3 class="fw-bolder mb-0 text-info">{{ number_format($tentativeBookings) }}</h3>
                    </div>
                    <div class="avatar avatar-l bg-info-subtle rounded-circle text-info d-flex align-items-center justify-content-center">
                        <span class="fas fa-hourglass-half fs-10"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Bookings -->
    <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card h-100 border-start border-4 border-secondary">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <h6 class="text-700 fs-11 text-uppercase mb-1">Completed</h6>
                        <h3 class="fw-bolder mb-0 text-secondary">{{ number_format($completedBookings) }}</h3>
                    </div>
                    <div class="avatar avatar-l bg-secondary-subtle rounded-circle text-secondary d-flex align-items-center justify-content-center">
                        <span class="fas fa-flag-checkered fs-10"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancelled Bookings -->
    <div class="col-sm-6 col-md-4 col-xl-2">
        <div class="card h-100 border-start border-4 border-warning">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-1">
                        <h6 class="text-700 fs-11 text-uppercase mb-1">Cancelled</h6>
                        <h3 class="fw-bolder mb-0 text-warning">{{ number_format($cancelledBookings) }}</h3>
                    </div>
                    <div class="avatar avatar-l bg-warning-subtle rounded-circle text-warning d-flex align-items-center justify-content-center">
                        <span class="fas fa-times-circle fs-10"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Financial KPI Cards -->
<div class="row g-3 mb-3">
    <!-- Total Booking Sales -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-700 fs-11 text-uppercase fw-semi-bold">Total Sales (Eligible)</div>
                        <h4 class="fw-bolder text-900 mt-1 mb-0">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($totalSales, 0) }}</h4>
                    </div>
                    <div class="badge badge-subtle-primary p-2 rounded-2 fs-9">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="fs-11 text-500 mt-2">
                    <span class="badge badge-subtle-success me-1">Confirmed + Completed</span> Bookings
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Received -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-700 fs-11 text-uppercase fw-semi-bold">Payments Received</div>
                        <h4 class="fw-bolder text-success mt-1 mb-0">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($netCashReceived, 0) }}</h4>
                    </div>
                    <div class="badge badge-subtle-success p-2 rounded-2 fs-9">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
                <div class="fs-11 text-500 mt-2">
                    Net cash collected from clients
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Customer Balances -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-700 fs-11 text-uppercase fw-semi-bold">Pending Balances</div>
                        <h4 class="fw-bolder text-danger mt-1 mb-0">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($pendingCustomerBalances, 0) }}</h4>
                    </div>
                    <div class="badge badge-subtle-danger p-2 rounded-2 fs-9">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                </div>
                <div class="fs-11 text-500 mt-2">
                    Receivables on active bookings
                </div>
            </div>
        </div>
    </div>

    <!-- Total Expenses & Net Profit -->
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-700 fs-11 text-uppercase fw-semi-bold">Estimated Net Profit</div>
                        <h4 class="fw-bolder {{ $estimatedNetProfit >= 0 ? 'text-primary' : 'text-danger' }} mt-1 mb-0">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($estimatedNetProfit, 0) }}
                        </h4>
                    </div>
                    <div class="badge badge-subtle-primary p-2 rounded-2 fs-9">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                </div>
                <div class="fs-11 text-500 mt-2">
                    Expenses: <span class="fw-bold text-danger">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($totalExpenses, 0) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Today's Events & Upcoming Events -->
<div class="row g-3 mb-3">
    <!-- Today's Schedule -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <h6 class="mb-0 fs-10 text-900 fw-bold">
                    <span class="fas fa-star text-warning me-2"></span>Today's Events ({{ now()->format('d M Y') }})
                </h6>
                <span class="badge badge-subtle-danger">{{ $todaysEvents->count() }} Event(s)</span>
            </div>
            <div class="card-body p-0">
                @if($todaysEvents->isEmpty())
                <div class="text-center py-4 text-500">
                    <i class="fas fa-glass-cheers fa-2x mb-2 text-300"></i>
                    <p class="mb-0 fs-10">No events scheduled for today.</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="bg-200">
                            <tr>
                                <th>Time</th>
                                <th>Customer</th>
                                <th>Event / Venue</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todaysEvents as $event)
                            <tr>
                                <td class="fw-bold fs-11 text-primary">
                                    {{ $event->event_start_time ? date('h:i A', strtotime($event->event_start_time)) : 'Full Day' }}
                                </td>
                                <td>
                                    <div class="fw-bold fs-11">{{ $event->customer->name }}</div>
                                    <div class="fs-11 text-500">{{ $event->customer->mobile_number }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-subtle-secondary">{{ $event->eventType->name ?? 'Event' }}</span>
                                    <div class="fs-11 text-600">{{ $event->venue ? $event->venue->name : ($event->venue_address ?? 'N/A') }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-subtle-{{ $event->status === 'confirmed' ? 'success' : ($event->status === 'tentative' ? 'info' : 'secondary') }}">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-falcon-default btn-sm py-0 px-2" href="{{ route('bookings.show', $event) }}">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Upcoming Events (Next 6) -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <h6 class="mb-0 fs-10 text-900 fw-bold">
                    <span class="fas fa-calendar-alt text-primary me-2"></span>Upcoming Events Schedule
                </h6>
                <a class="fs-11 text-primary" href="{{ route('bookings.index') }}">View All</a>
            </div>
            <div class="card-body p-0">
                @if($upcomingEvents->isEmpty())
                <div class="text-center py-4 text-500">
                    <i class="fas fa-calendar-check fa-2x mb-2 text-300"></i>
                    <p class="mb-0 fs-10">No upcoming events found.</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="bg-200">
                            <tr>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Event &amp; Stage</th>
                                <th>Venue</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingEvents as $ue)
                            <tr>
                                <td>
                                    <span class="badge badge-subtle-primary fs-11">{{ $ue->event_date->format('d M, Y') }}</span>
                                    <div class="fs-11 text-500">{{ $ue->event_date->format('l') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold fs-11">{{ $ue->customer->name }}</div>
                                    <div class="fs-11 text-500">{{ $ue->customer->mobile_number }}</div>
                                </td>
                                <td>
                                    <div class="fw-semi-bold">{{ $ue->eventType->name ?? 'Event' }}</div>
                                    <small class="text-muted">{{ $ue->decorationCategory->name ?? 'Custom' }}</small>
                                </td>
                                <td>
                                    <span class="fs-11 text-truncate d-inline-block" style="max-width: 130px;">
                                        {{ $ue->venue ? $ue->venue->name : ($ue->venue_address ?? 'N/A') }}
                                    </span>
                                    <div>
                                        <span class="badge badge-subtle-{{ $ue->booking_type === 'indoor' ? 'info' : 'warning' }} fs-11">
                                            {{ ucfirst($ue->booking_type) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-falcon-default btn-sm py-0 px-2" href="{{ route('bookings.show', $ue) }}">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Recent Bookings Table -->
<div class="card mb-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0 fs-10 text-900 fw-bold">
            <span class="fas fa-list text-primary me-2"></span>Recent Bookings
        </h6>
        <a class="btn btn-falcon-default btn-sm" href="{{ route('bookings.index') }}">
            View All Bookings <span class="fas fa-arrow-right ms-1"></span>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Booking #</th>
                        <th>Customer</th>
                        <th>Event Date</th>
                        <th>Event Type</th>
                        <th>Type</th>
                        <th class="text-end">Final Amount</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $booking)
                    <tr>
                        <td class="fw-bold">
                            <a href="{{ route('bookings.show', $booking) }}" class="text-primary">
                                {{ $booking->booking_number }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $booking->customer->name }}</div>
                            <small class="text-muted">{{ $booking->customer->mobile_number }}</small>
                        </td>
                        <td>
                            <span class="fw-semi-bold">{{ $booking->event_date->format('d M Y') }}</span>
                            <div class="fs-11 text-500">{{ $booking->event_start_time ? date('h:i A', strtotime($booking->event_start_time)) : '' }}</div>
                        </td>
                        <td>{{ $booking->eventType->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-subtle-{{ $booking->booking_type === 'indoor' ? 'info' : 'warning' }}">
                                {{ ucfirst($booking->booking_type) }}
                            </span>
                        </td>
                        <td class="text-end fw-bold">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->final_amount, 0) }}</td>
                        <td class="text-end text-success fw-semi-bold">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->total_paid, 0) }}</td>
                        <td class="text-end {{ $booking->remaining_balance > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->remaining_balance, 0) }}
                        </td>
                        <td>
                            @php
                                $statusClass = match($booking->status) {
                                    'confirmed' => 'success',
                                    'tentative' => 'info',
                                    'completed' => 'primary',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge badge-subtle-{{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-falcon-default" title="View Details">
                                    <span class="fas fa-eye"></span>
                                </a>
                                <a href="{{ route('bookings.confirmation-slip', $booking) }}" class="btn btn-falcon-default text-primary" title="Print Slip" target="_blank">
                                    <span class="fas fa-print"></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-500">
                            No bookings recorded yet. Click "New Booking" above to add your first stage decoration booking.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
