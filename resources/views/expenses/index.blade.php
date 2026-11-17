@extends('layouts.app')

@section('title', 'Expense Management')

@section('content')
<!-- Expense KPI Cards -->
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-md-4">
        <div class="card h-100 border-start border-4 border-danger">
            <div class="card-body p-3">
                <div class="fs-11 text-uppercase text-700 fw-semi-bold">Total Expenses (All Time)</div>
                <h3 class="fw-bolder mb-0 text-danger">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($totalExpenses, 0) }}</h3>
                <div class="fs-11 text-500 mt-1">Direct booking + general overhead</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="card h-100 border-start border-4 border-warning">
            <div class="card-body p-3">
                <div class="fs-11 text-uppercase text-700 fw-semi-bold">Direct Booking Expenses</div>
                <h3 class="fw-bolder mb-0 text-warning">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($bookingExpenses, 0) }}</h3>
                <div class="fs-11 text-500 mt-1">Tied directly to specific event stages</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="card h-100 border-start border-4 border-info">
            <div class="card-body p-3">
                <div class="fs-11 text-uppercase text-700 fw-semi-bold">General Business Overhead</div>
                <h3 class="fw-bolder mb-0 text-info">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($generalExpenses, 0) }}</h3>
                <div class="fs-11 text-500 mt-1">Shop, utility, fuel, non-booking expenses</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">
                    <span class="fas fa-receipt text-danger me-2"></span>Expense Register
                </h5>
            </div>
            <div class="col-8 col-sm-auto text-end ps-2">
                <a class="btn btn-danger btn-sm" href="{{ route('expenses.create') }}">
                    <span class="fas fa-plus me-1"></span> Record New Expense
                </a>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card-body bg-body-tertiary border-bottom py-2">
        <form method="GET" action="{{ route('expenses.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Expense #, description, remarks..." value="{{ $search }}">
            </div>

            <div class="col-md-2">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="scope" class="form-select form-select-sm">
                    <option value="">All Scopes (Direct + General)</option>
                    <option value="booking" {{ $expenseScope == 'booking' ? 'selected' : '' }}>Direct Booking Expenses</option>
                    <option value="general" {{ $expenseScope == 'general' ? 'selected' : '' }}>General Business Overhead</option>
                </select>
            </div>

            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}" placeholder="From Date" title="From Expense Date">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}" placeholder="To Date" title="To Expense Date">
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100" title="Filter">
                    <i class="fas fa-filter"></i>
                </button>
                @if($search || $categoryId || $expenseScope || $dateFrom || $dateTo)
                <a href="{{ route('expenses.index') }}" class="btn btn-falcon-default btn-sm" title="Clear">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Expenses Table -->
    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Expense #</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Scope / Booking</th>
                        <th>Method</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr>
                        <td class="fw-bold">{{ $expense->expense_number }}</td>
                        <td>{{ $expense->expense_date->format('d M Y') }}</td>
                        <td>
                            <span class="badge badge-subtle-primary">{{ $expense->category->name ?? 'General' }}</span>
                        </td>
                        <td>
                            <div class="fw-semi-bold">{{ $expense->description }}</div>
                            @if($expense->remarks)
                            <div class="fs-11 text-muted">{{ $expense->remarks }}</div>
                            @endif
                        </td>
                        <td>
                            @if($expense->booking_id)
                            <a href="{{ route('bookings.show', $expense->booking_id) }}" class="badge badge-subtle-warning text-decoration-none">
                                <i class="fas fa-bookmark me-1"></i> {{ $expense->booking->booking_number ?? 'Booking #' . $expense->booking_id }}
                            </a>
                            <div class="fs-11 text-500">{{ $expense->booking->customer->name ?? '' }}</div>
                            @else
                            <span class="badge badge-subtle-secondary">
                                <i class="fas fa-building me-1"></i> General Overhead
                            </span>
                            @endif
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</td>
                        <td class="text-end fw-bold text-danger">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($expense->amount, 0) }}
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                @if($expense->attachment_path)
                                <a href="{{ asset('storage/' . $expense->attachment_path) }}" class="btn btn-falcon-default" target="_blank" title="View Receipt Attachment">
                                    <span class="fas fa-paperclip"></span>
                                </a>
                                @endif
                                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-falcon-default" title="Edit">
                                    <span class="fas fa-edit"></span>
                                </a>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this expense record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-falcon-danger" type="submit" title="Delete">
                                        <span class="fas fa-trash-alt"></span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-500">No expenses recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($expenses->hasPages())
    <div class="card-footer bg-light py-2">
        {{ $expenses->links() }}
    </div>
    @endif
</div>
@endsection
