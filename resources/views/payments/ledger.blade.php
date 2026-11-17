@extends('layouts.app')

@section('title', 'Customer Ledger Statement')

@section('content')
<div class="card mb-3 no-print">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-auto">
                <h5 class="fs-9 mb-0 text-900">
                    <span class="fas fa-book text-primary me-2"></span>Customer Account Ledger
                </h5>
            </div>
            <div class="col-auto">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('payments.index') }}">
                    <span class="fas fa-arrow-left me-1"></span> Back to Payments
                </a>
            </div>
        </div>
    </div>
    <div class="card-body bg-body-tertiary">
        <form method="GET" action="{{ route('payments.ledger') }}" class="row g-2 align-items-center">
            <div class="col-md-6">
                <label class="form-label fw-bold" for="customer_id">Select Customer to View Statement</label>
                <select name="customer_id" id="customer_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Choose Customer --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ $customerId == $c->id ? 'selected' : '' }}>
                        {{ $c->name }} ({{ $c->mobile_number }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i> View Statement
                </button>
            </div>
        </form>
    </div>
</div>

@if($selectedCustomer)
<!-- Customer Statement Header -->
<div class="card mb-3">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
        <div>
            <h5 class="fs-9 mb-0 text-primary fw-bold">{{ $selectedCustomer->name }} - Statement of Account</h5>
            <div class="fs-11 text-muted">Phone: {{ $selectedCustomer->mobile_number }} | Address: {{ $selectedCustomer->address ?? 'N/A' }}</div>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <span class="fas fa-print me-1"></span> Print Statement
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Summary Numbers -->
        <div class="row g-3 mb-3 text-center">
            <div class="col-4">
                <div class="border rounded p-2 bg-light">
                    <div class="fs-11 text-muted text-uppercase fw-bold">Total Invoiced (Debits)</div>
                    <div class="fs-8 fw-bold text-900">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($selectedCustomer->total_booking_amount, 2) }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="border rounded p-2 bg-light">
                    <div class="fs-11 text-muted text-uppercase fw-bold">Total Received (Credits)</div>
                    <div class="fs-8 fw-bold text-success">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($selectedCustomer->total_paid_amount, 2) }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="border rounded p-2 bg-light">
                    <div class="fs-11 text-muted text-uppercase fw-bold">Net Outstanding Balance</div>
                    <div class="fs-8 fw-bolder {{ $selectedCustomer->outstanding_balance > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($selectedCustomer->outstanding_balance, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="table-responsive scrollbar">
            <table class="table table-bordered table-striped table-hover mb-0">
                <thead class="bg-200 text-900">
                    <tr>
                        <th style="width: 12%;">Date</th>
                        <th style="width: 15%;">Reference</th>
                        <th style="width: 15%;">Transaction Type</th>
                        <th>Description</th>
                        <th class="text-end" style="width: 13%;">Debit (Invoice)</th>
                        <th class="text-end" style="width: 13%;">Credit (Paid)</th>
                        <th class="text-end" style="width: 14%;">Running Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledgerEntries as $entry)
                    <tr>
                        <td>{{ $entry['date']->format('d M Y') }}</td>
                        <td class="fw-bold">
                            <a href="{{ $entry['link'] }}" class="text-primary" target="_blank">
                                {{ $entry['reference'] }}
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-subtle-{{ str_contains($entry['type'], 'Payment') ? 'success' : 'primary' }}">
                                {{ $entry['type'] }}
                            </span>
                        </td>
                        <td class="fs-11">{{ $entry['description'] }}</td>
                        <td class="text-end fw-semi-bold">
                            {{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '—' }}
                        </td>
                        <td class="text-end text-success fw-semi-bold">
                            {{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '—' }}
                        </td>
                        <td class="text-end fw-bold {{ $entry['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($entry['balance'], 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-500">No ledger transactions found for this customer.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($ledgerEntries->isNotEmpty())
                <tfoot class="bg-200 fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">Account Summary Totals:</td>
                        <td class="text-end">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($ledgerEntries->sum('debit'), 2) }}</td>
                        <td class="text-end text-success">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($ledgerEntries->sum('credit'), 2) }}</td>
                        <td class="text-end text-danger fs-10">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($selectedCustomer->outstanding_balance, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endif
@endsection
