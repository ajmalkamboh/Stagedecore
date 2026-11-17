<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventType;
use Illuminate\Validation\Rule;

class EventTypeController extends Controller
{
    public function index()
    {
        $eventTypes = EventType::withCount('bookings')->latest()->paginate(15);
        return view('settings.event_types.index', compact('eventTypes'));
    }

    public function create()
    {
        return view('settings.event_types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:event_types,name'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        EventType::create($validated);

        return redirect()->route('event-types.index')->with('success', 'Event Type added successfully.');
    }

    public function edit(EventType $eventType)
    {
        return view('settings.event_types.edit', compact('eventType'));
    }

    public function update(Request $request, EventType $eventType)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('event_types')->ignore($eventType->id)],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $eventType->update($validated);

        return redirect()->route('event-types.index')->with('success', 'Event Type updated successfully.');
    }

    public function toggleStatus(EventType $eventType)
    {
        $eventType->update(['is_active' => !$eventType->is_active]);

        $status = $eventType->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Event Type {$status} successfully.");
    }
}
