<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->receipt_number }}</title>
    <link href="{{ asset('falcon/assets/css/theme.min.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #212529;
            font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 13px;
        }
        .receipt-card {
            max-width: 680px;
            margin: 25px auto;
            background: #fff;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 0 15px rgba(0,0,0,0.06);
            border-top: 5px solid #00d27a;
        }
        .receipt-header {
            border-bottom: 2px dashed #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .table-receipt td, .table-receipt th {
            padding: 6px 10px;
            font-size: 12px;
        }
        @media print {
            body {
                background: #fff;
                margin: 0;
            }
            .receipt-card {
                box-shadow: none;
                margin: 0;
                padding: 10px;
                max-width: 100%;
                width: 100%;
                border-top: none;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print text-center my-3">
        <button onclick="window.print()" class="btn btn-success px-4 py-2 me-2">
            <span class="fas fa-print me-1"></span> Print Payment Receipt
        </button>
        <button onclick="window.close()" class="btn btn-secondary px-3 py-2">
            Close Window
        </button>
    </div>

    <div class="receipt-card">
        <!-- Business Info & Receipt Number -->
        <div class="receipt-header row align-items-center">
            <div class="col-7">
                <h4 class="fw-bolder text-primary mb-1">{{ $settings->business_name }}</h4>
                <div class="text-secondary small">{{ $settings->address }}</div>
                <div class="text-secondary small">
                    <strong>Mob:</strong> {{ $settings->mobile_number }}
                    @if($settings->alternate_number) | {{ $settings->alternate_number }} @endif
                </div>
            </div>
            <div class="col-5 text-end">
                <div class="badge bg-success-subtle text-success fs-10 px-3 py-1 mb-1">
                    {{ $payment->is_refund ? 'REFUND VOUCHER' : 'OFFICIAL PAYMENT RECEIPT' }}
                </div>
                <div class="fs-9 fw-bolder text-900">{{ $payment->receipt_number }}</div>
                <div class="fs-11 text-muted">Date: {{ $payment->payment_date->format('d M, Y') }}</div>
            </div>
        </div>

        <!-- Customer & Booking Info -->
        <table class="table table-sm table-borderless table-receipt mb-3">
            <tr>
                <th style="width: 25%;" class="text-muted text-uppercase">Received From:</th>
                <td class="fw-bold fs-10 text-primary">{{ $payment->customer->name }}</td>
                <th style="width: 20%;" class="text-muted text-uppercase">Booking Ref:</th>
                <td class="fw-bold">{{ $payment->booking->booking_number }}</td>
            </tr>
            <tr>
                <th class="text-muted text-uppercase">Phone:</th>
                <td>{{ $payment->customer->mobile_number }}</td>
                <th class="text-muted text-uppercase">Event Date:</th>
                <td>{{ $payment->booking->event_date->format('d M, Y') }} ({{ $payment->booking->eventType->name ?? 'Event' }})</td>
            </tr>
            <tr>
                <th class="text-muted text-uppercase">Payment Type:</th>
                <td>
                    <span class="badge badge-subtle-{{ $payment->is_refund ? 'danger' : 'success' }}">
                        {{ ucfirst($payment->payment_type) }} Payment
                    </span>
                </td>
                <th class="text-muted text-uppercase">Payment Mode:</th>
                <td class="fw-bold">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
            </tr>
            @if($payment->transaction_reference)
            <tr>
                <th class="text-muted text-uppercase">Reference / TID:</th>
                <td colspan="3">{{ $payment->transaction_reference }}</td>
            </tr>
            @endif
            @if($payment->remarks)
            <tr>
                <th class="text-muted text-uppercase">Remarks:</th>
                <td colspan="3" class="fst-italic text-secondary">{{ $payment->remarks }}</td>
            </tr>
            @endif
        </table>

        <!-- Amount Box -->
        <div class="border rounded p-3 bg-light my-3">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="fs-11 text-uppercase text-muted fw-bold">Amount Received</div>
                    <div class="fs-6 fw-bolder {{ $payment->is_refund ? 'text-danger' : 'text-success' }}">
                        {{ $payment->is_refund ? '-' : '' }} {{ $settings->currency_symbol }} {{ number_format($payment->amount, 2) }}
                    </div>
                </div>
                <div class="col-6 text-end">
                    <div class="fs-11 text-uppercase text-muted fw-bold">Remaining Booking Balance</div>
                    <div class="fs-8 fw-bold {{ $payment->booking->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                        {{ $settings->currency_symbol }} {{ number_format($payment->booking->remaining_balance, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Total Summary Table -->
        <table class="table table-bordered table-sm table-receipt mb-4">
            <tr class="bg-light text-center">
                <th>Final Booking Amount</th>
                <th>Total Paid Till Date</th>
                <th>Current Remaining Balance</th>
            </tr>
            <tr class="text-center fw-bold">
                <td>{{ $settings->currency_symbol }} {{ number_format($payment->booking->final_amount, 2) }}</td>
                <td class="text-success">{{ $settings->currency_symbol }} {{ number_format($payment->booking->total_paid, 2) }}</td>
                <td class="{{ $payment->booking->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                    {{ $settings->currency_symbol }} {{ number_format($payment->booking->remaining_balance, 2) }}
                </td>
            </tr>
        </table>

        <!-- Signature Section -->
        <div class="row pt-4 mt-3 text-center">
            <div class="col-6">
                <div style="border-top: 1px dashed #6c757d; width: 75%; margin: 0 auto; padding-top: 4px;">
                    <small>Client Signature</small>
                </div>
            </div>
            <div class="col-6">
                <div style="border-top: 1px dashed #6c757d; width: 75%; margin: 0 auto; padding-top: 4px;">
                    <small>Received By: <strong>{{ $payment->receiver->name ?? 'Staff' }}</strong></small>
                </div>
            </div>
        </div>

        <!-- Receipt Footer Note -->
        <div class="text-center text-muted small mt-4 pt-2 border-top" style="font-size: 10px;">
            {{ $settings->receipt_footer ?? 'Computer generated receipt. Thank you for your business!' }}
        </div>
    </div>
</body>
</html>
