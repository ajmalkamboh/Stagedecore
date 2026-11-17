<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Commission Voucher - {{ $booking->booking_number }}</title>
    <link href="{{ asset('falcon/assets/css/theme.min.css') }}" rel="stylesheet">
    <link href="{{ asset('falcon/vendors/fontawesome-free/all.min.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #212529;
            font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 13px;
        }
        .voucher-card {
            max-width: 720px;
            margin: 25px auto;
            background: #fff;
            padding: 35px;
            border-radius: 6px;
            box-shadow: 0 0 15px rgba(0,0,0,0.06);
            border-top: 5px solid #f5803e;
        }
        .voucher-header {
            border-bottom: 2px dashed #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .table-voucher td, .table-voucher th {
            padding: 6px 10px;
            font-size: 12px;
        }
        @media print {
            body {
                background: #fff;
                margin: 0;
            }
            .voucher-card {
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
        <button onclick="window.print()" class="btn btn-warning px-4 py-2 me-2 text-white">
            <span class="fas fa-print me-1"></span> Print Commission Voucher
        </button>
        <button onclick="window.close()" class="btn btn-secondary px-3 py-2">
            Close Window
        </button>
    </div>

    <div class="voucher-card">
        <!-- Business Header -->
        <div class="voucher-header">
            <div class="row align-items-center">
                <div class="col-8">
                    @if($businessSetting->logo_path)
                    <img src="{{ asset('storage/' . $businessSetting->logo_path) }}" alt="Logo" style="max-height: 48px;" class="mb-2">
                    @endif
                    <h4 class="mb-1 fw-bold text-dark">{{ $businessSetting->business_name }}</h4>
                    <p class="mb-0 text-muted fs-11">
                        {{ $businessSetting->address }}<br>
                        Phone / WhatsApp: {{ $businessSetting->mobile_number }}
                        @if($businessSetting->alternate_mobile) | {{ $businessSetting->alternate_mobile }} @endif
                    </p>
                </div>
                <div class="col-4 text-end">
                    <div class="badge bg-warning text-white fs-11 px-3 py-2 text-uppercase mb-2">Venue Commission Voucher</div>
                    <div class="fs-11 text-muted">Date: <strong>{{ $booking->commission_paid_at ? $booking->commission_paid_at->format('d M, Y') : date('d M, Y') }}</strong></div>
                    <div class="fs-11 text-muted">Booking: <strong>{{ $booking->booking_number }}</strong></div>
                    @if($booking->commissionExpense)
                    <div class="fs-11 text-muted">Expense Ref: <strong>{{ $booking->commissionExpense->expense_number }}</strong></div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Venue & Recipient Details -->
        <div class="row mb-3 bg-light p-3 rounded mx-0">
            <div class="col-6">
                <h6 class="fs-11 text-uppercase text-muted fw-bold mb-1">Payable To (Venue / Marquee):</h6>
                <div class="fw-bold fs-9 text-dark">{{ $booking->venue?->name ?? 'Venue Partner' }}</div>
                <div class="fs-11 text-muted">Venue Type: {{ $booking->venue?->venue_type ?? 'Event Venue' }}</div>
                @if($booking->venue?->address)
                <div class="fs-11 text-muted">{{ $booking->venue->address }}</div>
                @endif
                @if($booking->venue?->contact_number)
                <div class="fs-11 text-muted">Contact: {{ $booking->venue->contact_number }}</div>
                @endif
            </div>
            <div class="col-6">
                <h6 class="fs-11 text-uppercase text-muted fw-bold mb-1">Payment Received By:</h6>
                <div class="fw-bold fs-9 text-primary">{{ $booking->commission_recipient ?? 'Venue Management' }}</div>
                <div class="fs-11 text-muted mt-1">Payment Mode: <strong>{{ strtoupper(str_replace('_', ' ', $booking->commission_payment_method ?? 'Cash')) }}</strong></div>
                @if($booking->commission_notes)
                <div class="fs-11 text-muted">Notes: {{ $booking->commission_notes }}</div>
                @endif
            </div>
        </div>

        <!-- Event Particulars -->
        <div class="table-responsive mb-3">
            <table class="table table-bordered table-voucher mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Event Description</th>
                        <th>Client Name</th>
                        <th>Event Date</th>
                        <th class="text-end">Event Booking Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>{{ $booking->eventType?->name ?? 'Event' }} Stage Decoration</strong>
                            <div class="fs-11 text-muted">{{ $booking->decorationCategory?->name ?? 'Stage Package' }}</div>
                        </td>
                        <td>{{ $booking->customer?->name ?? 'Client' }}</td>
                        <td>{{ $booking->event_date->format('d M, Y') }}</td>
                        <td class="text-end fw-bold">{{ $businessSetting->currency_symbol ?? 'Rs.' }} {{ number_format($booking->final_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Commission Calculation & Amount Paid -->
        <div class="row justify-content-end mb-4">
            <div class="col-7">
                <table class="table table-sm table-voucher table-borderless">
                    <tr>
                        <td class="text-muted">Agreed Commission Terms:</td>
                        <td class="text-end fw-bold">
                            @if($booking->commission_type === 'percentage')
                                {{ number_format($booking->commission_rate, 1) }}% of Total Event Booking
                            @else
                                Fixed Cash Referral Commission
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Commission Payable:</td>
                        <td class="text-end fw-bold">{{ $businessSetting->currency_symbol ?? 'Rs.' }} {{ number_format($booking->commission_amount, 2) }}</td>
                    </tr>
                    <tr class="border-top border-bottom border-2">
                        <td class="fs-8 fw-bold text-dark py-2">Net Amount Paid:</td>
                        <td class="fs-8 fw-bolder text-end text-success py-2">
                            {{ $businessSetting->currency_symbol ?? 'Rs.' }} {{ number_format($booking->commission_paid_amount > 0 ? $booking->commission_paid_amount : $booking->commission_amount, 2) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Acknowledgment Statement -->
        <div class="p-2 bg-light border rounded mb-4 fs-11 text-muted text-center">
            This voucher serves as an official acknowledgment that the referral/hall commission mentioned above has been settled in full for the event staged at the venue.
        </div>

        <!-- Signatures -->
        <div class="row pt-4 mt-3 text-center">
            <div class="col-6">
                <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                    <strong class="fs-11">Authorized By (Decorator)</strong><br>
                    <span class="fs-11 text-muted">{{ $businessSetting->owner_name ?? $businessSetting->business_name }}</span>
                </div>
            </div>
            <div class="col-6">
                <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto; padding-top: 5px;">
                    <strong class="fs-11">Received By (Hall / Marquee Representative)</strong><br>
                    <span class="fs-11 text-muted">Signature &amp; Stamp</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 pt-3 border-top fs-11 text-muted">
            {{ $businessSetting->receipt_footer ?? 'Thank you for your valuable partnership!' }} &bull; Printed on {{ date('d M, Y h:i A') }}
        </div>
    </div>
</body>
</html>
