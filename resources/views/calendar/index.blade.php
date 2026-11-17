@extends('layouts.app')

@section('title', 'Booking Calendar & Availability')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-sm-auto mb-2 mb-sm-0">
                <h5 class="fs-9 mb-0 text-900">
                    <span class="fas fa-calendar-alt text-primary me-2"></span>Stage Booking &amp; Availability Calendar
                </h5>
                <div class="fs-11 text-500">Live visual calendar of indoor and outdoor event schedules</div>
            </div>
            <div class="col-sm-auto d-flex align-items-center gap-2">
                <a class="btn btn-primary btn-sm" href="{{ route('bookings.create') }}">
                    <span class="fas fa-plus me-1"></span> New Booking
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card-body bg-body-tertiary border-bottom py-2">
        <div class="row g-2 align-items-center">
            <!-- Status Filter -->
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <label class="input-group-text bg-white" for="statusFilter"><i class="fas fa-tag me-1 text-primary"></i> Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="all">All Statuses</option>
                        <option value="confirmed">Confirmed Only</option>
                        <option value="tentative">Tentative Only</option>
                        <option value="completed">Completed Only</option>
                    </select>
                </div>
            </div>

            <!-- Booking Type Filter (Indoor/Outdoor) -->
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <label class="input-group-text bg-white" for="typeFilter"><i class="fas fa-layer-group me-1 text-primary"></i> Setup</label>
                    <select class="form-select" id="typeFilter">
                        <option value="all">Indoor &amp; Outdoor</option>
                        <option value="indoor">Indoor (Halls/Hotels)</option>
                        <option value="outdoor">Outdoor (Lawn/Grounds)</option>
                    </select>
                </div>
            </div>

            <!-- Legend -->
            <div class="col-md-6 text-md-end">
                <div class="d-inline-flex flex-wrap gap-2 align-items-center fs-11">
                    <span><span class="badge" style="background-color: #00d27a;">&nbsp;</span> Confirmed</span>
                    <span><span class="badge" style="background-color: #27bcfd;">&nbsp;</span> Tentative</span>
                    <span><span class="badge" style="background-color: #2c7be5;">&nbsp;</span> Completed</span>
                    <span><span class="badge" style="background-color: #e63757;">&nbsp;</span> Cancelled</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="card-body p-3">
        <div id="calendar" style="min-height: 650px;"></div>
    </div>
</div>

<!-- Event Details Quick View Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fs-9" id="eventModalLabel">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary mb-0" id="m_bookingNumber"></h5>
                    <span class="badge" id="m_statusBadge"></span>
                </div>

                <table class="table table-sm table-borderless fs-10 mb-0">
                    <tr>
                        <th class="text-500" style="width: 35%;">Customer:</th>
                        <td class="fw-bold" id="m_customerName"></td>
                    </tr>
                    <tr>
                        <th class="text-500">Phone:</th>
                        <td id="m_customerPhone"></td>
                    </tr>
                    <tr>
                        <th class="text-500">Event Type:</th>
                        <td id="m_eventType"></td>
                    </tr>
                    <tr>
                        <th class="text-500">Setup Type:</th>
                        <td><span class="badge badge-subtle-primary" id="m_bookingType"></span></td>
                    </tr>
                    <tr>
                        <th class="text-500">Venue:</th>
                        <td id="m_venue"></td>
                    </tr>
                    <tr>
                        <th class="text-500">Stage Category:</th>
                        <td id="m_decorCategory"></td>
                    </tr>
                    <tr>
                        <th class="text-500">Final Amount:</th>
                        <td class="fw-bold" id="m_finalAmount"></td>
                    </tr>
                    <tr>
                        <th class="text-500">Remaining Balance:</th>
                        <td class="fw-bold text-danger" id="m_balance"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary btn-sm" id="m_viewBtn">
                    <span class="fas fa-eye me-1"></span> View Full Booking
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('falcon/vendors/fullcalendar/index.global.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');

    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));

    function getEventsUrl() {
        const status = statusFilter.value;
        const type = typeFilter.value;
        return `{{ route('calendar.events') }}?status=${encodeURIComponent(status)}&booking_type=${encodeURIComponent(type)}`;
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        buttonText: {
            today: 'Today',
            month: 'Month',
            week: 'Week',
            list: 'List'
        },
        height: 'auto',
        events: getEventsUrl(),
        eventClick: function(info) {
            info.jsEvent.preventDefault(); // don't navigate directly
            const props = info.event.extendedProps;

            document.getElementById('m_bookingNumber').textContent = props.bookingNumber;
            document.getElementById('m_customerName').textContent = props.customerName;
            document.getElementById('m_customerPhone').textContent = props.customerPhone;
            document.getElementById('m_eventType').textContent = props.eventType;
            document.getElementById('m_bookingType').textContent = props.bookingType;
            document.getElementById('m_venue').textContent = props.venue;
            document.getElementById('m_decorCategory').textContent = props.decorCategory;
            document.getElementById('m_finalAmount').textContent = '{{ $globalSettings->currency_symbol ?? "Rs." }} ' + props.finalAmount;
            document.getElementById('m_balance').textContent = '{{ $globalSettings->currency_symbol ?? "Rs." }} ' + props.balance;
            
            const badge = document.getElementById('m_statusBadge');
            badge.textContent = props.status;
            badge.className = 'badge bg-' + (props.status.toLowerCase() === 'confirmed' ? 'success' : (props.status.toLowerCase() === 'tentative' ? 'info' : 'secondary'));

            document.getElementById('m_viewBtn').href = props.viewUrl;

            eventModal.show();
        }
    });

    calendar.render();

    function refreshCalendar() {
        calendar.removeAllEventSources();
        calendar.addEventSource(getEventsUrl());
    }

    statusFilter.addEventListener('change', refreshCalendar);
    typeFilter.addEventListener('change', refreshCalendar);
});
</script>
@endpush
