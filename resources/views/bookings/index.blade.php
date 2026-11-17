@extends('layouts.app')

@section('title', 'Stage Bookings')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">
                    <span class="fas fa-calendar-check text-primary me-2"></span>Stage Decoration Bookings
                </h5>
            </div>
            <div class="col-8 col-sm-auto text-end ps-2">
                <a class="btn btn-primary btn-sm" href="{{ route('bookings.create') }}">
                    <span class="fas fa-plus me-1"></span> New Booking
                </a>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="card-body bg-body-tertiary border-bottom py-2">
        <form method="GET" action="{{ route('bookings.index') }}" class="row g-2 align-items-center">
            <!-- Search -->
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Booking #, customer name, mobile..." value="{{ $search }}">
            </div>

            <!-- Status Filter -->
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="tentative" {{ $status == 'tentative' ? 'selected' : '' }}>Tentative</option>
                    <option value="confirmed" {{ $status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Booking Type -->
            <div class="col-md-2">
                <select name="booking_type" class="form-select form-select-sm">
                    <option value="">All Types (Indoor/Outdoor)</option>
                    <option value="indoor" {{ $bookingType == 'indoor' ? 'selected' : '' }}>Indoor Events</option>
                    <option value="outdoor" {{ $bookingType == 'outdoor' ? 'selected' : '' }}>Outdoor Events</option>
                </select>
            </div>

            <!-- Date Range -->
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" placeholder="Event Date From" value="{{ $dateFrom }}" title="From Event Date">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" placeholder="Event Date To" value="{{ $dateTo }}" title="To Event Date">
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100" title="Apply Filters">
                    <i class="fas fa-filter"></i>
                </button>
                @if($status || $bookingType || $dateFrom || $dateTo || $search)
                <a href="{{ route('bookings.index') }}" class="btn btn-falcon-default btn-sm" title="Clear Filters">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Booking #</th>
                        <th>Customer</th>
                        <th>Event Date &amp; Time</th>
                        <th>Event / Stage</th>
                        <th>Venue / Type</th>
                        <th class="text-end">Final Amount</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr>
                        <td class="fw-bold">
                            <a href="{{ route('bookings.show', $booking) }}" class="text-primary">
                                {{ $booking->booking_number }}
                            </a>
                            <div class="fs-11 text-500">Booked: {{ $booking->booking_date->format('d M') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold">
                                <a href="{{ route('customers.show', $booking->customer_id) }}" class="text-900">
                                    {{ $booking->customer->name }}
                                </a>
                            </div>
                            <div class="fs-11 text-500">{{ $booking->customer->mobile_number }}</div>
                        </td>
                        <td>
                            <span class="badge badge-subtle-primary fs-11">{{ $booking->event_date->format('d M, Y') }}</span>
                            <div class="fs-11 text-600 mt-1">
                                {{ $booking->event_start_time ? date('h:i A', strtotime($booking->event_start_time)) : 'Time N/A' }}
                            </div>
                        </td>
                        <td>
                            <div class="fw-semi-bold">{{ $booking->eventType->name ?? 'N/A' }}</div>
                            <div class="fs-11 text-muted">{{ $booking->decorationCategory->name ?? 'Custom Stage' }}</div>
                        </td>
                        <td>
                            <div class="fs-11 text-truncate" style="max-width: 140px;">
                                {{ $booking->venue ? $booking->venue->name : ($booking->venue_address ?? 'N/A') }}
                            </div>
                            <span class="badge badge-subtle-{{ $booking->booking_type === 'indoor' ? 'info' : 'warning' }} fs-11">
                                {{ ucfirst($booking->booking_type) }}
                            </span>
                        </td>
                        <td class="text-end fw-bold">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->final_amount, 0) }}
                        </td>
                        <td class="text-end text-success fw-semi-bold">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->total_paid, 0) }}
                        </td>
                        <td class="text-end {{ $booking->remaining_balance > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($booking->remaining_balance, 0) }}
                        </td>
                        <td>
                            @php
                                $bClass = match($booking->status) {
                                    'confirmed' => 'success',
                                    'tentative' => 'info',
                                    'completed' => 'primary',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge badge-subtle-{{ $bClass }}">{{ ucfirst($booking->status) }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-falcon-default" title="View Details">
                                    <span class="fas fa-eye"></span>
                                </a>
                                @if($booking->remaining_balance > 0 && $booking->status !== 'cancelled')
                                <a href="{{ route('payments.create', ['booking_id' => $booking->id]) }}" class="btn btn-falcon-success" title="Add Payment">
                                    <span class="fas fa-hand-holding-usd"></span>
                                </a>
                                @endif
                                <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-falcon-default" title="Edit Booking">
                                    <span class="fas fa-edit"></span>
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
                            No bookings match the specified criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($bookings->hasPages())
    <div class="card-footer bg-light py-2">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@endsection
