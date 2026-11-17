<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation Slip - {{ $booking->booking_number }}</title>
    <link href="{{ asset('falcon/assets/css/theme.min.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #212529;
            font-family: 'Open Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 13px;
        }
        .slip-container {
            max-width: 820px;
            margin: 20px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 6px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }
        .border-bottom-heavy {
            border-bottom: 2px solid #2c3e50;
        }
        .section-title {
            background-color: #f1f4f8;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            border-left: 3px solid #2a7be4;
        }
        .table-custom td, .table-custom th {
            padding: 5px 8px;
            font-size: 12px;
        }
        @media print {
            body {
                background: #fff;
                margin: 0;
            }
            .slip-container {
                box-shadow: none;
                margin: 0;
                padding: 15px;
                max-width: 100%;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print text-center my-3">
        <button onclick="window.print()" class="btn btn-primary px-4 py-2 me-2">
            <span class="fas fa-print me-1"></span> Print Confirmation Slip
        </button>
        <button onclick="window.close()" class="btn btn-secondary px-3 py-2">
            Close Window
        </button>
    </div>

    <div class="slip-container">
        <!-- Business Header -->
        <div class="row align-items-center border-bottom-heavy pb-3 mb-3">
            <div class="col-8">
                <h3 class="fw-bolder text-primary mb-1">{{ $settings->business_name }}</h3>
                <div class="text-secondary small">{{ $settings->address }}</div>
                <div class="text-secondary small">
                    <strong>Phone:</strong> {{ $settings->mobile_number }} 
                    @if($settings->alternate_number) | <strong>WhatsApp:</strong> {{ $settings->alternate_number }} @endif
                    @if($settings->email) | <strong>Email:</strong> {{ $settings->email }} @endif
                </div>
            </div>
            <div class="col-4 text-end">
                <div class="border rounded p-2 bg-light text-center">
                    <div class="fs-11 text-uppercase text-muted fw-bold">Booking Confirmation</div>
                    <div class="fs-8 fw-bolder text-primary">{{ $booking->booking_number }}</div>
                    <div class="fs-11 text-muted">Date: {{ $booking->booking_date->format('d M, Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Customer & Event Grid -->
        <div class="row g-3 mb-3">
            <div class="col-6">
                <div class="section-title">Client Details</div>
                <table class="w-100 table-custom">
                    <tr>
                        <th style="width: 35%;">Client Name:</th>
                        <td class="fw-bold">{{ $booking->customer->name }}</td>
                    </tr>
                    <tr>
                        <th>Mobile Number:</th>
                        <td>{{ $booking->customer->mobile_number }}</td>
                    </tr>
                    @if($booking->customer->alternate_mobile)
                    <tr>
                        <th>Alternate Phone:</th>
                        <td>{{ $booking->customer->alternate_mobile }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Address:</th>
                        <td>{{ $booking->customer->address ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>

            <div class="col-6">
                <div class="section-title">Event Schedule &amp; Venue</div>
                <table class="w-100 table-custom">
                    <tr>
                        <th style="width: 35%;">Event Date:</th>
                        <td class="fw-bold text-primary">{{ $booking->event_date->format('l, d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Event Timings:</th>
                        <td>
                            @if($booking->event_start_time)
                                {{ date('h:i A', strtotime($booking->event_start_time)) }}
                                @if($booking->event_end_time)
                                    — {{ date('h:i A', strtotime($booking->event_end_time)) }}
                                @endif
                            @else
                                Full Day Setup
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Event &amp; Setup:</th>
                        <td>{{ $booking->eventType->name ?? 'Event' }} ({{ ucfirst($booking->booking_type) }} Setup)</td>
                    </tr>
                    <tr>
                        <th>Venue Location:</th>
                        <td class="fw-semi-bold">
                            {{ $booking->venue ? $booking->venue->name : '' }}
                            @if($booking->venue_address)
                                ({{ $booking->venue_address }})
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Stage Decoration Specs -->
        <div class="mb-3">
            <div class="section-title">Stage Decoration Specifications</div>
            <table class="table table-bordered table-sm table-custom mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 30%;">Stage Package / Category</th>
                        <th>Decoration Specifications &amp; Inclusions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold text-primary align-top">
                            {{ $booking->decorationCategory->name ?? 'Custom Decoration Setup' }}
                        </td>
                        <td>
                            <div class="text-800" style="white-space: pre-line;">{{ $booking->decoration_details ?? 'Standard package setup as agreed with client.' }}</div>
                            @if($booking->special_instructions)
                            <div class="mt-2 text-danger small fst-italic">
                                <strong>Logistics / Notes:</strong> {{ $booking->special_instructions }}
                            </div>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Financial Summary -->
        <div class="row justify-content-end mb-4">
            <div class="col-6">
                <div class="section-title">Billing &amp; Payment Summary</div>
                <table class="table table-sm table-bordered table-custom mb-0">
                    <tr>
                        <td>Total Stage Booking Amount:</td>
                        <td class="text-end fw-bold">{{ $settings->currency_symbol }} {{ number_format($booking->total_amount, 2) }}</td>
                    </tr>
                    @if($booking->discount > 0)
                    <tr>
                        <td>Concession / Discount:</td>
                        <td class="text-end text-danger">- {{ $settings->currency_symbol }} {{ number_format($booking->discount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="table-light">
                        <td class="fw-bold">Final Amount Payable:</td>
                        <td class="text-end fw-bolder text-primary">{{ $settings->currency_symbol }} {{ number_format($booking->final_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Advance / Payments Received:</td>
                        <td class="text-end text-success fw-bold">{{ $settings->currency_symbol }} {{ number_format($booking->total_paid, 2) }}</td>
                    </tr>
                    <tr class="table-warning">
                        <td class="fw-bold text-danger">Remaining Balance Due:</td>
                        <td class="text-end fw-bolder text-danger fs-10">{{ $settings->currency_symbol }} {{ number_format($booking->remaining_balance, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Terms & Conditions -->
        @if($settings->terms_and_conditions)
        <div class="mb-4">
            <div class="section-title">Terms &amp; Conditions</div>
            <div class="text-muted small border rounded p-2 bg-light" style="white-space: pre-line; font-size: 11px;">
                {{ $settings->terms_and_conditions }}
            </div>
        </div>
        @endif

        <!-- Signatures -->
        <div class="row pt-4 mt-4 text-center">
            <div class="col-6">
                <div style="border-top: 1px dashed #6c757d; width: 80%; margin: 0 auto; padding-top: 5px;">
                    <strong>Customer Signature</strong>
                </div>
            </div>
            <div class="col-6">
                <div style="border-top: 1px dashed #6c757d; width: 80%; margin: 0 auto; padding-top: 5px;">
                    <strong>Authorized Signatory / Management</strong>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-muted small mt-4 pt-2 border-top" style="font-size: 10px;">
            {{ $settings->receipt_footer ?? 'Thank you for choosing our stage decoration services.' }}
        </div>
    </div>
</body>
</html>
