<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DecorationCategory;

class DecorationCategoryController extends Controller
{
    public function index()
    {
        $categories = DecorationCategory::withCount('bookings')->latest()->paginate(15);
        return view('settings.decoration_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('settings.decoration_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        DecorationCategory::create($validated);

        return redirect()->route('decoration-categories.index')->with('success', 'Decoration Category added successfully.');
    }

    public function edit(DecorationCategory $decorationCategory)
    {
        return view('settings.decoration_categories.edit', compact('decorationCategory'));
    }

    public function update(Request $request, DecorationCategory $decorationCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ]);

        $decorationCategory->update($validated);

        return redirect()->route('decoration-categories.index')->with('success', 'Decoration Category updated successfully.');
    }

    public function toggleStatus(DecorationCategory $decorationCategory)
    {
        $decorationCategory->update(['is_active' => !$decorationCategory->is_active]);

        $status = $decorationCategory->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Decoration Category {$status} successfully.");
    }
}
