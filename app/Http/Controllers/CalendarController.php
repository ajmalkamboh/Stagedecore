<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        return view('calendar.index');
    }

    public function events(Request $request)
    {
        $status = $request->query('status'); // all, confirmed, tentative
        $bookingType = $request->query('booking_type'); // all, indoor, outdoor

        $bookings = Booking::with(['customer', 'eventType', 'venue', 'decorationCategory'])
            ->when($status && $status !== 'all', fn($q) => $q->where('status', $status))
            ->when($bookingType && $bookingType !== 'all', fn($q) => $q->where('booking_type', $bookingType))
            ->get();

        $events = $bookings->map(function ($b) {
            $statusColors = [
                'confirmed' => '#00d27a',
                'tentative' => '#27bcfd',
                'completed' => '#2c7be5',
                'cancelled' => '#e63757',
            ];

            $color = $statusColors[$b->status] ?? '#748194';

            $start = $b->event_date->format('Y-m-d');
            if ($b->event_start_time) {
                $start .= 'T' . date('H:i:s', strtotime($b->event_start_time));
            }

            $end = null;
            if ($b->event_end_time) {
                $end = $b->event_date->format('Y-m-d') . 'T' . date('H:i:s', strtotime($b->event_end_time));
            }

            $title = $b->customer->name . ' - ' . ($b->eventType->name ?? 'Event');

            return [
                'id' => $b->id,
                'title' => $title,
                'start' => $start,
                'end' => $end,
                'allDay' => empty($b->event_start_time),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#ffffff',
                'url' => route('bookings.show', $b),
                'extendedProps' => [
                    'bookingNumber' => $b->booking_number,
                    'customerName' => $b->customer->name,
                    'customerPhone' => $b->customer->mobile_number,
                    'eventType' => $b->eventType->name ?? 'N/A',
                    'venue' => $b->venue ? $b->venue->name : ($b->venue_address ?? 'N/A'),
                    'bookingType' => ucfirst($b->booking_type),
                    'decorCategory' => $b->decorationCategory->name ?? 'Custom',
                    'status' => ucfirst($b->status),
                    'finalAmount' => number_format($b->final_amount, 0),
                    'balance' => number_format($b->remaining_balance, 0),
                    'viewUrl' => route('bookings.show', $b),
                ],
            ];
        });

        return response()->json($events);
    }
}
