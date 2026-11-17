@extends('layouts.app')

@section('title', 'Financial Reports & Profit/Loss')

@section('content')
<div class="card mb-3 no-print">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-sm-auto mb-2 mb-sm-0">
                <h5 class="fs-9 mb-0 text-900">
                    <span class="fas fa-chart-pie text-primary me-2"></span>Financial Reports &amp; Profit/Loss Analysis
                </h5>
                <div class="fs-11 text-500">Real transaction audits, accrual sales analysis, and verified cash movements</div>
            </div>
            <div class="col-sm-auto">
                <a class="btn btn-falcon-warning btn-sm me-2" href="{{ route('commissions.report') }}">
                    <span class="fas fa-hand-holding-usd me-1"></span> Venue Commissions
                </a>
                <button onclick="window.print()" class="btn btn-falcon-default btn-sm">
                    <span class="fas fa-print me-1"></span> Print Report
                </button>
            </div>
        </div>
    </div>

    <!-- Date Range & Tab Controls -->
    <div class="card-body bg-body-tertiary border-bottom py-2">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white">From:</span>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white">To:</span>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-sync-alt me-1"></i> Apply Filter
                </button>
            </div>
        </form>
    </div>

    <!-- 10 Report Navigation Tabs -->
    <div class="card-body p-2 border-bottom">
        <ul class="nav nav-pills fs-11" id="reportTabs">
            <li class="nav-item">
                <a class="nav-link py-1 px-2 {{ $tab == 'profit-loss' ? 'active' : '' }}" href="{{ route('reports.index', ['tab' => 'profit-loss', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                    <i class="fas fa-balance-scale me-1"></i> Profit &amp; Loss
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-2 {{ $tab == 'sales-daily' ? 'active' : '' }}" href="{{ route('reports.index', ['tab' => 'sales-daily', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                    1. Daily Sales
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-2 {{ $tab == 'sales-monthly' ? 'active' : '' }}" href="{{ route('reports.index', ['tab' => 'sales-monthly', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                    2. Monthly Sales
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-2 {{ $tab == 'sales-datewise' ? 'active' : '' }}" href="{{ route('reports.index', ['tab' => 'sales-datewise', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                    3. Date-wise Sales
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-2 {{ $tab == 'payments' ? 'active' : '' }}" href="{{ route('reports.index', ['tab' => 'payments', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                    4. Payments Received
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-2 {{ $tab == 'outstanding' ? 'active' : '' }}" href="{{ route('reports.index', ['tab' => 'outstanding', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                    5. Outstanding Balances
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-2 {{ $tab == 'expenses-daily' ? 'active' : '' }}" href="{{ route('reports.index', ['tab' => 'expenses-daily', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                    6. Daily Expenses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-2 {{ $tab == 'expenses-monthly' ? 'active' : '' }}" href="{{ route('reports.index', ['tab' => 'expenses-monthly', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                    7. Monthly Expenses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-2 {{ $tab == 'booking-profit' ? 'active' : '' }}" href="{{ route('reports.index', ['tab' => 'booking-profit', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                    8. Booking-wise Profit
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-1 px-2 {{ $tab == 'cancelled' ? 'active' : '' }}" href="{{ route('reports.index', ['tab' => 'cancelled', 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}">
                    10. Cancelled Bookings
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- ========================================== -->
<!-- REPORT 9: OVERALL PROFIT / LOSS & CASH MOVEMENT -->
<!-- ========================================== -->
@if($tab === 'profit-loss')
<div class="row g-3 mb-3">
    <!-- Section A: Sales-Based Net Profit -->
    <div class="col-lg-6">
        <div class="card h-100 border">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fs-10 fw-bold text-primary">
                    <span class="fas fa-file-invoice me-2"></span>Part A: Accrual Sales-Based Profit
                </h6>
                <div class="fs-11 text-muted">Eligible Sales - Total Business Expenses ({{ date('d M Y', strtotime($dateFrom)) }} to {{ date('d M Y', strtotime($dateTo)) }})</div>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0 fs-10">
                    <tr>
                        <td>Eligible Sales ({{ $reportData['eligibleBookingCount'] }} Confirmed/Completed Bookings):</td>
                        <td class="text-end fw-bold text-primary">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData['totalSales'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 text-muted">Less Direct Booking Expenses:</td>
                        <td class="text-end text-danger">- {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData['bookingDirectExpenses'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 text-muted">Less General Business Overhead:</td>
                        <td class="text-end text-danger">- {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData['generalOverheadExpenses'], 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td class="fw-bold">Total Operating Expenses:</td>
                        <td class="text-end fw-bold text-danger">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData['totalExpenses'], 2) }}</td>
                    </tr>
                    <tr class="border-top border-2 table-light">
                        <td class="fw-bolder fs-9">Estimated Net Profit / (Loss):</td>
                        <td class="text-end fw-bolder fs-8 {{ $reportData['accrualNetProfit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData['accrualNetProfit'], 2) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Section B: Cash Flow & Movement -->
    <div class="col-lg-6">
        <div class="card h-100 border">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fs-10 fw-bold text-success">
                    <span class="fas fa-hand-holding-usd me-2"></span>Part B: Actual Cash Movement
                </h6>
                <div class="fs-11 text-muted">Actual Receipts - Refunds Paid - Expenses Paid</div>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0 fs-10">
                    <tr>
                        <td>Actual Client Payments Received:</td>
                        <td class="text-end fw-bold text-success">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData['actualReceipts'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3 text-muted">Less Customer Refunds Paid:</td>
                        <td class="text-end text-danger">- {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData['refundsPaid'], 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <td class="fw-bold">Net Cash Inflow:</td>
                        <td class="text-end fw-bold text-success">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData['netCashReceived'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Less Actual Expenses Paid:</td>
                        <td class="text-end text-danger">- {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData['totalExpenses'], 2) }}</td>
                    </tr>
                    <tr class="border-top border-2 table-light">
                        <td class="fw-bolder fs-9">Net Cash Movement:</td>
                        <td class="text-end fw-bolder fs-8 {{ $reportData['netCashMovement'] >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData['netCashMovement'], 2) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- ========================================== -->
<!-- REPORT 1 & 3: DAILY / DATE-WISE SALES -->
<!-- ========================================== -->
@if(in_array($tab, ['sales-daily', 'sales-datewise']))
<div class="card mb-3">
    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fs-10 fw-bold text-900">
            <span class="fas fa-chart-line text-primary me-2"></span>
            {{ $tab == 'sales-daily' ? 'Daily Sales Report' : 'Date-wise Sales Report' }}
            ({{ date('d M Y', strtotime($dateFrom)) }} to {{ date('d M Y', strtotime($dateTo)) }})
        </h6>
        <span class="badge badge-subtle-primary">{{ $reportData->count() }} Event(s)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Event Date</th>
                        <th>Booking #</th>
                        <th>Customer</th>
                        <th>Event Type</th>
                        <th>Setup</th>
                        <th class="text-end">Gross Amount</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Final Sales</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $row)
                    <tr>
                        <td class="fw-bold">{{ $row->event_date->format('d M Y') }}</td>
                        <td><a href="{{ route('bookings.show', $row) }}" class="text-primary">{{ $row->booking_number }}</a></td>
                        <td>{{ $row->customer->name }}</td>
                        <td>{{ $row->eventType->name ?? 'N/A' }}</td>
                        <td><span class="badge badge-subtle-{{ $row->booking_type === 'indoor' ? 'info' : 'warning' }}">{{ ucfirst($row->booking_type) }}</span></td>
                        <td class="text-end">{{ number_format($row->total_amount, 2) }}</td>
                        <td class="text-end text-danger">{{ number_format($row->discount, 2) }}</td>
                        <td class="text-end fw-bold text-primary">{{ number_format($row->final_amount, 2) }}</td>
                        <td class="text-end text-success">{{ number_format($row->total_paid, 2) }}</td>
                        <td class="text-end {{ $row->remaining_balance > 0 ? 'text-danger' : 'text-muted' }}">{{ number_format($row->remaining_balance, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-500">No eligible sales recorded for this date range.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($reportData->isNotEmpty())
                <tfoot class="bg-200 fw-bold">
                    <tr>
                        <td colspan="5" class="text-end">Totals:</td>
                        <td class="text-end">{{ number_format($reportData->sum('total_amount'), 2) }}</td>
                        <td class="text-end text-danger">{{ number_format($reportData->sum('discount'), 2) }}</td>
                        <td class="text-end text-primary fs-10">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData->sum('final_amount'), 2) }}</td>
                        <td class="text-end text-success">{{ number_format($reportData->sum->total_paid, 2) }}</td>
                        <td class="text-end text-danger">{{ number_format($reportData->sum->remaining_balance, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endif

<!-- ========================================== -->
<!-- REPORT 2: MONTHLY SALES REPORT -->
<!-- ========================================== -->
@if($tab === 'sales-monthly')
<div class="card mb-3">
    <div class="card-header bg-light py-2">
        <h6 class="mb-0 fs-10 fw-bold text-900">
            <span class="fas fa-calendar-alt text-primary me-2"></span>Monthly Sales Summary
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Month</th>
                        <th class="text-center">Total Confirmed Events</th>
                        <th class="text-end">Gross Booked Amount</th>
                        <th class="text-end">Discounts Granted</th>
                        <th class="text-end">Net Final Sales</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $row)
                    <tr>
                        <td class="fw-bold fs-10 text-primary">{{ date('F Y', strtotime($row->month . '-01')) }}</td>
                        <td class="text-center"><span class="badge badge-subtle-primary">{{ $row->total_events }}</span></td>
                        <td class="text-end">{{ number_format($row->gross_amount, 2) }}</td>
                        <td class="text-end text-danger">{{ number_format($row->total_discount, 2) }}</td>
                        <td class="text-end fw-bolder text-primary fs-10">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($row->total_sales, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-500">No monthly sales data found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- ========================================== -->
<!-- REPORT 4: TOTAL PAYMENTS RECEIVED -->
<!-- ========================================== -->
@if($tab === 'payments')
<div class="card mb-3">
    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fs-10 fw-bold text-900">
            <span class="fas fa-hand-holding-usd text-success me-2"></span>Total Payments Received Report
        </h6>
        <span class="badge badge-subtle-success">{{ $reportData->count() }} Transaction(s)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Date</th>
                        <th>Receipt #</th>
                        <th>Customer</th>
                        <th>Booking #</th>
                        <th>Type</th>
                        <th>Payment Method</th>
                        <th>Reference / Remarks</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $p)
                    <tr>
                        <td>{{ $p->payment_date->format('d M Y') }}</td>
                        <td class="fw-bold"><a href="{{ route('payments.receipt', $p) }}" target="_blank" class="text-primary">{{ $p->receipt_number }}</a></td>
                        <td>{{ $p->customer->name }}</td>
                        <td><a href="{{ route('bookings.show', $p->booking_id) }}">{{ $p->booking->booking_number ?? '#' . $p->booking_id }}</a></td>
                        <td><span class="badge badge-subtle-{{ $p->is_refund ? 'danger' : 'success' }}">{{ ucfirst($p->payment_type) }}</span></td>
                        <td>{{ ucfirst(str_replace('_', ' ', $p->payment_method)) }}</td>
                        <td class="fs-11 text-600">{{ $p->transaction_reference }}</td>
                        <td class="text-end fw-bold {{ $p->is_refund ? 'text-danger' : 'text-success' }}">
                            {{ $p->is_refund ? '-' : '+' }} {{ number_format($p->amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-500">No payment transactions found in this date range.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($reportData->isNotEmpty())
                <tfoot class="bg-200 fw-bold">
                    <tr>
                        <td colspan="7" class="text-end">Net Cash Received:</td>
                        <td class="text-end text-success fs-10">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData->where('is_refund', false)->sum('amount') - $reportData->where('is_refund', true)->sum('amount'), 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endif

<!-- ========================================== -->
<!-- REPORT 5: OUTSTANDING BALANCES REPORT -->
<!-- ========================================== -->
@if($tab === 'outstanding')
<div class="card mb-3">
    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fs-10 fw-bold text-danger">
            <span class="fas fa-exclamation-circle text-danger me-2"></span>Outstanding Customer Balances Report
        </h6>
        <span class="badge badge-subtle-danger">{{ $reportData->count() }} Booking(s) with Balance</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Booking #</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>Event Date</th>
                        <th>Status</th>
                        <th class="text-end">Final Amount</th>
                        <th class="text-end">Total Paid</th>
                        <th class="text-end text-danger">Pending Balance</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $row)
                    <tr>
                        <td class="fw-bold"><a href="{{ route('bookings.show', $row) }}" class="text-primary">{{ $row->booking_number }}</a></td>
                        <td class="fw-bold"><a href="{{ route('customers.show', $row->customer_id) }}">{{ $row->customer->name }}</a></td>
                        <td>{{ $row->customer->mobile_number }}</td>
                        <td>{{ $row->event_date->format('d M Y') }}</td>
                        <td><span class="badge badge-subtle-{{ $row->status === 'confirmed' ? 'success' : 'info' }}">{{ ucfirst($row->status) }}</span></td>
                        <td class="text-end">{{ number_format($row->final_amount, 2) }}</td>
                        <td class="text-end text-success">{{ number_format($row->total_paid, 2) }}</td>
                        <td class="text-end fw-bolder text-danger fs-10">{{ number_format($row->remaining_balance, 2) }}</td>
                        <td class="text-end">
                            <a href="{{ route('payments.create', ['booking_id' => $row->id]) }}" class="btn btn-falcon-success btn-sm py-0 px-2">
                                Collect
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-success fw-bold">All customer balances are fully cleared!</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($reportData->isNotEmpty())
                <tfoot class="bg-200 fw-bold">
                    <tr>
                        <td colspan="7" class="text-end">Total Outstanding Receivables:</td>
                        <td class="text-end text-danger fs-9">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData->sum->remaining_balance, 2) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endif

<!-- ========================================== -->
<!-- REPORT 6: DAILY EXPENSE REPORT -->
<!-- ========================================== -->
@if($tab === 'expenses-daily')
<div class="card mb-3">
    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fs-10 fw-bold text-900">
            <span class="fas fa-receipt text-danger me-2"></span>Daily Expense Report
        </h6>
        <span class="badge badge-subtle-danger">{{ $reportData->count() }} Expenses</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Date</th>
                        <th>Expense #</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Booking / Scope</th>
                        <th>Method</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $e)
                    <tr>
                        <td>{{ $e->expense_date->format('d M Y') }}</td>
                        <td class="fw-bold">{{ $e->expense_number }}</td>
                        <td><span class="badge badge-subtle-primary">{{ $e->category->name ?? 'General' }}</span></td>
                        <td>{{ $e->description }}</td>
                        <td>
                            @if($e->booking_id)
                            <a href="{{ route('bookings.show', $e->booking_id) }}" class="text-primary">{{ $e->booking->booking_number ?? '#' . $e->booking_id }}</a>
                            @else
                            <span class="badge badge-subtle-secondary">Overhead</span>
                            @endif
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $e->payment_method)) }}</td>
                        <td class="text-end fw-bold text-danger">{{ number_format($e->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-500">No expenses found in this date range.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($reportData->isNotEmpty())
                <tfoot class="bg-200 fw-bold">
                    <tr>
                        <td colspan="6" class="text-end">Total Expenses:</td>
                        <td class="text-end text-danger fs-10">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData->sum('amount'), 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endif

<!-- ========================================== -->
<!-- REPORT 7: MONTHLY EXPENSE REPORT -->
<!-- ========================================== -->
@if($tab === 'expenses-monthly')
<div class="card mb-3">
    <div class="card-header bg-light py-2">
        <h6 class="mb-0 fs-10 fw-bold text-900">
            <span class="fas fa-calendar-alt text-danger me-2"></span>Monthly Expense Summary
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Month</th>
                        <th class="text-center">Total Expense Records</th>
                        <th class="text-end">Total Disbursed Expense</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $row)
                    <tr>
                        <td class="fw-bold text-primary">{{ date('F Y', strtotime($row->month . '-01')) }}</td>
                        <td class="text-center"><span class="badge badge-subtle-secondary">{{ $row->total_count }}</span></td>
                        <td class="text-end fw-bolder text-danger fs-10">{{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($row->total_expense, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-500">No monthly expense data found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- ========================================== -->
<!-- REPORT 8: BOOKING-WISE PROFIT REPORT -->
<!-- ========================================== -->
@if($tab === 'booking-profit')
<div class="card mb-3">
    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fs-10 fw-bold text-900">
            <span class="fas fa-chart-bar text-primary me-2"></span>Booking-wise Profit Analysis
        </h6>
        <span class="badge badge-subtle-primary">{{ $reportData->count() }} Bookings</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Booking #</th>
                        <th>Customer</th>
                        <th>Event Date</th>
                        <th>Event Type</th>
                        <th class="text-end">Final Amount (Revenue)</th>
                        <th class="text-end">Direct Expenses</th>
                        <th class="text-end">Estimated Profit</th>
                        <th class="text-end">Profit Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $b)
                    @php
                        $expenses = $b->total_expenses;
                        $profit = $b->final_amount - $expenses;
                        $margin = $b->final_amount > 0 ? ($profit / $b->final_amount) * 100 : 0;
                    @endphp
                    <tr>
                        <td class="fw-bold"><a href="{{ route('bookings.show', $b) }}" class="text-primary">{{ $b->booking_number }}</a></td>
                        <td>{{ $b->customer->name }}</td>
                        <td>{{ $b->event_date->format('d M Y') }}</td>
                        <td>{{ $b->eventType->name ?? 'N/A' }}</td>
                        <td class="text-end fw-bold">{{ number_format($b->final_amount, 2) }}</td>
                        <td class="text-end text-danger">{{ number_format($expenses, 2) }}</td>
                        <td class="text-end fw-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($profit, 2) }}
                        </td>
                        <td class="text-end">
                            <span class="badge badge-subtle-{{ $margin >= 40 ? 'success' : ($margin >= 15 ? 'warning' : 'danger') }}">
                                {{ number_format($margin, 1) }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-500">No booking profit records found in this range.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($reportData->isNotEmpty())
                <tfoot class="bg-200 fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">Totals:</td>
                        <td class="text-end text-primary">{{ number_format($reportData->sum('final_amount'), 2) }}</td>
                        <td class="text-end text-danger">{{ number_format($reportData->sum->total_expenses, 2) }}</td>
                        <td class="text-end text-success fs-10">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($reportData->sum->estimated_profit, 2) }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endif

<!-- ========================================== -->
<!-- REPORT 10: CANCELLED BOOKINGS REPORT -->
<!-- ========================================== -->
@if($tab === 'cancelled')
<div class="card mb-3">
    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fs-10 fw-bold text-danger">
            <span class="fas fa-times-circle text-danger me-2"></span>Cancelled Bookings Report
        </h6>
        <span class="badge badge-subtle-danger">{{ $reportData->count() }} Cancelled</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Booking #</th>
                        <th>Customer</th>
                        <th>Original Event Date</th>
                        <th>Cancelled Date</th>
                        <th>Cancellation Reason</th>
                        <th class="text-end">Billed Amount</th>
                        <th class="text-end">Refunds Disbursed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $cb)
                    <tr>
                        <td class="fw-bold"><a href="{{ route('bookings.show', $cb) }}" class="text-primary">{{ $cb->booking_number }}</a></td>
                        <td>{{ $cb->customer->name }}</td>
                        <td>{{ $cb->event_date->format('d M Y') }}</td>
                        <td>{{ $cb->cancelled_at ? $cb->cancelled_at->format('d M Y') : 'N/A' }}</td>
                        <td class="fs-11 text-danger">{{ $cb->cancellation_reason ?? 'Not specified' }}</td>
                        <td class="text-end text-muted">{{ number_format($cb->final_amount, 2) }}</td>
                        <td class="text-end text-danger fw-bold">
                            {{ number_format($cb->payments()->where('is_refund', true)->sum('amount'), 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-500">No cancelled bookings in this date range.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
