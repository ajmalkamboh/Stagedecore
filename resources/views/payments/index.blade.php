@extends('layouts.app')

@section('title', 'Payments Management')

@section('content')
<!-- KPI Header Cards -->
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-md-4">
        <div class="card h-100 border-start border-4 border-success">
            <div class="card-body p-3">
                <div class="fs-11 text-uppercase text-700 fw-semi-bold">Gross Payments Received</div>
                <h3 class="fw-bolder mb-0 text-success">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($totalCollected, 0) }}</h3>
                <div class="fs-11 text-500 mt-1">Total customer cash collected</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="card h-100 border-start border-4 border-danger">
            <div class="card-body p-3">
                <div class="fs-11 text-uppercase text-700 fw-semi-bold">Total Refunds Disbursed</div>
                <h3 class="fw-bolder mb-0 text-danger">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($totalRefunded, 0) }}</h3>
                <div class="fs-11 text-500 mt-1">Returned on cancellations/adjustments</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-4">
        <div class="card h-100 border-start border-4 border-primary">
            <div class="card-body p-3">
                <div class="fs-11 text-uppercase text-700 fw-semi-bold">Net Cash Inflow</div>
                <h3 class="fw-bolder mb-0 text-primary">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($netCollected, 0) }}</h3>
                <div class="fs-11 text-500 mt-1">Net customer revenue retained</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">
                    <span class="fas fa-money-bill-wave text-success me-2"></span>Customer Payments Register
                </h5>
            </div>
            <div class="col-8 col-sm-auto text-end ps-2">
                <a class="btn btn-falcon-default btn-sm me-1" href="{{ route('payments.ledger') }}">
                    <span class="fas fa-book me-1"></span> Customer Ledger
                </a>
                <a class="btn btn-success btn-sm" href="{{ route('payments.create') }}">
                    <span class="fas fa-plus me-1"></span> Record Payment
                </a>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="card-body bg-body-tertiary border-bottom py-2">
        <form method="GET" action="{{ route('payments.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Receipt #, customer, booking #, ref..." value="{{ $search }}">
            </div>

            <div class="col-md-2">
                <select name="payment_type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="advance" {{ $paymentType == 'advance' ? 'selected' : '' }}>Advance</option>
                    <option value="partial" {{ $paymentType == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="final" {{ $paymentType == 'final' ? 'selected' : '' }}>Final</option>
                    <option value="refund" {{ $paymentType == 'refund' ? 'selected' : '' }}>Refund</option>
                </select>
            </div>

            <div class="col-md-2">
                <select name="payment_method" class="form-select form-select-sm">
                    <option value="">All Methods</option>
                    <option value="cash" {{ $paymentMethod == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank" {{ $paymentMethod == 'bank' ? 'selected' : '' }}>Bank Deposit</option>
                    <option value="online_transfer" {{ $paymentMethod == 'online_transfer' ? 'selected' : '' }}>Online Transfer</option>
                    <option value="cheque" {{ $paymentMethod == 'cheque' ? 'selected' : '' }}>Cheque</option>
                </select>
            </div>

            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}" placeholder="From Date" title="Payment Date From">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}" placeholder="To Date" title="Payment Date To">
            </div>

            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100" title="Apply Filters">
                    <i class="fas fa-filter"></i>
                </button>
                @if($search || $paymentType || $paymentMethod || $dateFrom || $dateTo)
                <a href="{{ route('payments.index') }}" class="btn btn-falcon-default btn-sm" title="Clear Filters">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Payments List -->
    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Receipt #</th>
                        <th>Payment Date</th>
                        <th>Customer</th>
                        <th>Booking #</th>
                        <th>Type</th>
                        <th>Method</th>
                        <th>Reference / Remarks</th>
                        <th>Received By</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td class="fw-bold">
                            <a href="{{ route('payments.receipt', $payment) }}" target="_blank" class="text-primary">
                                {{ $payment->receipt_number }}
                            </a>
                        </td>
                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('customers.show', $payment->customer_id) }}" class="fw-semi-bold text-900">
                                {{ $payment->customer->name }}
                            </a>
                            <div class="fs-11 text-500">{{ $payment->customer->mobile_number }}</div>
                        </td>
                        <td>
                            <a href="{{ route('bookings.show', $payment->booking_id) }}" class="text-primary">
                                {{ $payment->booking->booking_number ?? '#' . $payment->booking_id }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-subtle-{{ $payment->is_refund ? 'danger' : 'success' }}">
                                {{ ucfirst($payment->payment_type) }}
                            </span>
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td class="fs-11 text-600">
                            {{ $payment->transaction_reference ?? '—' }}
                            @if($payment->remarks)
                            <div class="text-muted fst-italic">{{ Str::limit($payment->remarks, 30) }}</div>
                            @endif
                        </td>
                        <td class="fs-11">{{ $payment->receiver->name ?? 'Staff' }}</td>
                        <td class="text-end fw-bold {{ $payment->is_refund ? 'text-danger' : 'text-success' }}">
                            {{ $payment->is_refund ? '-' : '+' }} {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($payment->amount, 0) }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('payments.receipt', $payment) }}" class="btn btn-falcon-default btn-sm" target="_blank" title="Print Receipt">
                                <span class="fas fa-print text-primary"></span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-500">
                            No payment transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payments->hasPages())
    <div class="card-footer bg-light py-2">
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endsection
