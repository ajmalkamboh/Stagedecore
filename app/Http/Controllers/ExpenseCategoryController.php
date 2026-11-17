<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $categories = ExpenseCategory::withCount('expenses')
            ->when($search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('settings.expense_categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('settings.expense_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:expense_categories,name'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        ExpenseCategory::create($validated);

        return redirect()->route('expense-categories.index')->with('success', 'Expense Category added successfully.');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return view('settings.expense_categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('expense_categories')->ignore($expenseCategory->id)],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $expenseCategory->update($validated);

        return redirect()->route('expense-categories.index')->with('success', 'Expense Category updated successfully.');
    }

    public function toggleStatus(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->update(['is_active' => !$expenseCategory->is_active]);

        $status = $expenseCategory->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Expense Category {$status} successfully.");
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->expenses()->exists()) {
            return back()->with('error', 'Cannot delete this expense category because it has associated expense records. Deactivate it instead.');
        }

        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')->with('success', 'Expense Category deleted successfully.');
    }
}
