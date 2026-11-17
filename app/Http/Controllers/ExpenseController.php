<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Booking;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $expenseScope = $request->query('scope'); // all, booking, general
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();

        $expenses = Expense::with(['category', 'booking.customer', 'recorder'])
            ->when($search, function ($q, $search) {
                $q->where('expense_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%");
            })
            ->when($categoryId, fn($q) => $q->where('expense_category_id', $categoryId))
            ->when($expenseScope === 'booking', fn($q) => $q->whereNotNull('booking_id'))
            ->when($expenseScope === 'general', fn($q) => $q->whereNull('booking_id'))
            ->when($dateFrom, fn($q) => $q->whereDate('expense_date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('expense_date', '<=', $dateTo))
            ->latest('expense_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        // Calculate KPI summary figures
        $totalExpenses = Expense::sum('amount');
        $bookingExpenses = Expense::whereNotNull('booking_id')->sum('amount');
        $generalExpenses = Expense::whereNull('booking_id')->sum('amount');

        return view('expenses.index', compact(
            'expenses',
            'categories',
            'search',
            'categoryId',
            'expenseScope',
            'dateFrom',
            'dateTo',
            'totalExpenses',
            'bookingExpenses',
            'generalExpenses'
        ));
    }

    public function create(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $selectedBooking = null;

        if ($bookingId) {
            $selectedBooking = Booking::with('customer')->find($bookingId);
        }

        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $bookings = Booking::with('customer')
            ->whereIn('status', ['confirmed', 'tentative', 'completed'])
            ->latest('event_date')
            ->get();

        $generatedExpenseNumber = Expense::generateExpenseNumber();

        return view('expenses.create', compact('categories', 'bookings', 'selectedBooking', 'generatedExpenseNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => ['required', 'date'],
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,bank,online_transfer,other'],
            'remarks' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('expenses', 'public');
        }

        DB::transaction(function () use ($validated, $attachmentPath) {
            Expense::create([
                'expense_number' => Expense::generateExpenseNumber(),
                'expense_category_id' => $validated['expense_category_id'],
                'booking_id' => $validated['booking_id'] ?? null,
                'expense_date' => $validated['expense_date'],
                'description' => $validated['description'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'remarks' => $validated['remarks'] ?? null,
                'attachment_path' => $attachmentPath,
                'recorded_by' => auth()->id(),
            ]);
        });

        if (!empty($validated['booking_id'])) {
            return redirect()->route('bookings.show', $validated['booking_id'])
                ->with('success', 'Booking direct expense recorded successfully.');
        }

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();
        $bookings = Booking::with('customer')
            ->whereIn('status', ['confirmed', 'tentative', 'completed'])
            ->latest('event_date')
            ->get();

        return view('expenses.edit', compact('expense', 'categories', 'bookings'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'expense_date' => ['required', 'date'],
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,bank,online_transfer,other'],
            'remarks' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('attachment')) {
            if ($expense->attachment_path && Storage::disk('public')->exists($expense->attachment_path)) {
                Storage::disk('public')->delete($expense->attachment_path);
            }
            $validated['attachment_path'] = $request->file('attachment')->store('expenses', 'public');
        }

        $expense->update($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense record updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $bookingId = $expense->booking_id;
        $expense->delete();

        if ($bookingId) {
            return back()->with('success', 'Booking expense removed.');
        }

        return redirect()->route('expenses.index')->with('success', 'Expense record deleted.');
    }
}
