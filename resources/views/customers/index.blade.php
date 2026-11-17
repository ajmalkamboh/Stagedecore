@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">
                    <span class="fas fa-users text-primary me-2"></span>Customer Directory
                </h5>
            </div>
            <div class="col-8 col-sm-auto text-end ps-2">
                <a class="btn btn-primary btn-sm" href="{{ route('customers.create') }}">
                    <span class="fas fa-user-plus me-1"></span> Add New Customer
                </a>
            </div>
        </div>
    </div>
    
    <!-- Search Bar -->
    <div class="card-body bg-body-tertiary border-bottom py-2">
        <form method="GET" action="{{ route('customers.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search by customer name or mobile number..." value="{{ $search }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                    @if($search)
                    <a href="{{ route('customers.index') }}" class="btn btn-falcon-default" title="Clear Search">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Customer Name</th>
                        <th>Mobile Number</th>
                        <th>Address</th>
                        <th class="text-center">Bookings</th>
                        <th class="text-end">Total Billed</th>
                        <th class="text-end">Paid Amount</th>
                        <th class="text-end">Outstanding</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td class="fw-bold">
                            <a href="{{ route('customers.show', $customer) }}" class="text-primary">
                                {{ $customer->name }}
                            </a>
                        </td>
                        <td>
                            <span class="fw-semi-bold">{{ $customer->mobile_number }}</span>
                            @if($customer->alternate_mobile)
                            <div class="fs-11 text-500">{{ $customer->alternate_mobile }}</div>
                            @endif
                        </td>
                        <td class="fs-11 text-600">
                            {{ Str::limit($customer->address ?? '—', 30) }}
                        </td>
                        <td class="text-center">
                            <span class="badge badge-subtle-primary">{{ $customer->bookings->count() }}</span>
                        </td>
                        <td class="text-end fw-semi-bold">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($customer->total_booking_amount, 0) }}
                        </td>
                        <td class="text-end text-success fw-semi-bold">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($customer->total_paid_amount, 0) }}
                        </td>
                        <td class="text-end {{ $customer->outstanding_balance > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($customer->outstanding_balance, 0) }}
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-falcon-default" title="View Profile">
                                    <span class="fas fa-eye"></span>
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-falcon-default" title="Edit">
                                    <span class="fas fa-edit"></span>
                                </a>
                                <a href="{{ route('bookings.create', ['customer_id' => $customer->id]) }}" class="btn btn-falcon-primary" title="New Booking">
                                    <span class="fas fa-calendar-plus"></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-500">
                            No customers found. Click "Add New Customer" to register your clients.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
    <div class="card-footer bg-light py-2">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection
