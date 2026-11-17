<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $customers = Customer::with(['bookings', 'payments'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%")
                    ->orWhere('alternate_mobile', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'mobile_number' => ['required', 'string', 'max:50'],
            'alternate_mobile' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        // Duplicate mobile warning check
        $existing = Customer::where('mobile_number', $validated['mobile_number'])->first();
        if ($existing && !$request->boolean('confirm_duplicate')) {
            return back()->withInput()->with('duplicate_warning', [
                'id' => $existing->id,
                'name' => $existing->name,
                'mobile' => $existing->mobile_number,
                'message' => "A customer with mobile number '{$existing->mobile_number}' already exists ({$existing->name}). Click 'Confirm & Add Anyway' if this is a different person.",
            ]);
        }

        $customer = Customer::create($validated);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'bookings' => function ($query) {
                $query->with(['eventType', 'venue', 'decorationCategory', 'payments', 'expenses'])->latest();
            },
            'payments' => function ($query) {
                $query->with('booking')->latest();
            }
        ]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'mobile_number' => ['required', 'string', 'max:50'],
            'alternate_mobile' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer details updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->bookings()->whereIn('status', ['tentative', 'confirmed'])->exists()) {
            return back()->with('error', 'Cannot delete customer with active tentative or confirmed bookings.');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer removed successfully.');
    }
}
