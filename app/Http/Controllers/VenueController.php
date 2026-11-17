<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;

class VenueController extends Controller
{
    public function index()
    {
        $venues = Venue::withCount('bookings')->latest()->paginate(15);
        return view('settings.venues.index', compact('venues'));
    }

    public function create()
    {
        $venueTypes = ['Marriage Hall', 'Banquet Hall', 'Hotel', 'Home', 'Outdoor Ground', 'Other'];
        return view('settings.venues.create', compact('venueTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'venue_type' => ['required', 'string'],
            'address' => ['nullable', 'string'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        Venue::create($validated);

        return redirect()->route('venues.index')->with('success', 'Venue created successfully.');
    }

    public function edit(Venue $venue)
    {
        $venueTypes = ['Marriage Hall', 'Banquet Hall', 'Hotel', 'Home', 'Outdoor Ground', 'Other'];
        return view('settings.venues.edit', compact('venue', 'venueTypes'));
    }

    public function update(Request $request, Venue $venue)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'venue_type' => ['required', 'string'],
            'address' => ['nullable', 'string'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $venue->update($validated);

        return redirect()->route('venues.index')->with('success', 'Venue updated successfully.');
    }

    public function toggleStatus(Venue $venue)
    {
        $venue->update(['is_active' => !$venue->is_active]);

        $status = $venue->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Venue {$status} successfully.");
    }
}
