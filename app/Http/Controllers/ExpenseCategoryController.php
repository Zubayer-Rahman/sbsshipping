<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ExpenseCategory::orderBy('parent_category')->orderBy('name');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage    = $request->input('per_page', 50);
        $categories = $query->paginate($perPage);

        return view('expenses.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        ExpenseCategory::create([
            'name'            => $request->name,
            'code'            => $request->code,
            'parent_category' => $request->parent_category,
        ]);

        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $expenseCategory->update([
            'name'            => $request->name,
            'code'            => $request->code,
            'parent_category' => $request->parent_category,
        ]);

        return back()->with('success', 'Category updated.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();
        return back()->with('success', 'Category deleted.');
    }
}