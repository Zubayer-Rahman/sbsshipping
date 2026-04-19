<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    // ── List ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Expense::latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('expense_ref', 'like', "%$s%")
                    ->orWhere('job_ref_no', 'like', "%$s%")
                    ->orWhere('expense_category', 'like', "%$s%")
                    ->orWhere('sub_category', 'like', "%$s%")
                    ->orWhere('expense_for', 'like', "%$s%");
            });
        }
        if ($request->filled('category'))    $query->where('expense_category', $request->category);
        if ($request->filled('status'))      $query->where('payment_status', $request->status);
        if ($request->filled('date_from'))   $query->whereDate('expense_date', '>=', $request->date_from);
        if ($request->filled('date_to'))     $query->whereDate('expense_date', '<=', $request->date_to);

        $perPage  = $request->input('per_page', 50);
        $expenses = $query->paginate($perPage);

        return view('expenses.list', compact('expenses'));
    }

    // ── Create form ───────────────────────────────────────────────────────────
    public function create()
    {
        $users      = User::orderBy('name')->get();
        $contacts   = Contact::where('is_active', true)->orderBy('business_name')->get();
        $jobs       = DB::table('sbs_jobs')->orderByDesc('id')->get(['id', 'job_no', 'job_id']);
        $categories = ExpenseCategory::orderBy('parent_category')->orderBy('name')->get();

        return view('expenses.create', compact('users', 'contacts', 'jobs', 'categories'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $request->except('_token', 'job_ids');

        // Handle multiple job selections — before redirect
        $jobIds = array_filter((array) $request->input('job_ids', []));
        $data['job_id']     = !empty($jobIds) ? $jobIds[0] : null;
        $data['job_ref_no'] = $request->input('job_ref_no');

        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')
                ->store('expense_docs', 'public');
        }

        $data['user_id']  = Auth::id();
        $data['added_by'] = Auth::user()->name;
        $data['is_refund']    = $request->boolean('is_refund');
        $data['is_recurring'] = $request->boolean('is_recurring');

        foreach ($data as $k => $v) {
            if ($v === '') $data[$k] = null;
        }

        Expense::create($data);

        return redirect()->route('expenses.list')
            ->with('success', 'Expense added successfully!');
    }

    // ── Edit ──────────────────────────────────────────────────────────────────
    public function edit(Expense $expense)
    {
        $users      = User::orderBy('name')->get();
        $contacts   = Contact::where('is_active', true)->orderBy('business_name')->get();
        $jobs       = DB::table('sbs_jobs')->orderByDesc('id')->get(['id', 'job_no', 'job_id']);
        $categories = ExpenseCategory::orderBy('parent_category')->orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'users', 'contacts', 'jobs', 'categories'));
    }

    // ── Update ────────────────────────────────────────────────────────────────
    public function update(Request $request, Expense $expense)
    {
        $data = $request->except('_token', '_method');

        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')
                ->store('expense_docs', 'public');
        }

        foreach ($data as $k => $v) {
            if ($v === '') $data[$k] = null;
        }

        $data['is_refund']    = $request->boolean('is_refund');
        $data['is_recurring'] = $request->boolean('is_recurring');

        // Recalculate payment due
        $total   = floatval($data['total_amount']   ?? $expense->total_amount);
        $paid    = floatval($data['payment_amount'] ?? $expense->payment_amount);
        $due     = $total - $paid;
        $data['payment_due']    = max(0, $due);
        $data['payment_status'] = $due <= 0 ? 'Paid' : ($paid > 0 ? 'Partial' : 'Due');

        $expense->update($data);

        return redirect()->route('expenses.list')
            ->with('success', 'Expense updated.');
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Expense deleted.');
    }

    // ── AJAX: subcategories for a parent ─────────────────────────────────────
    public function subcategories(Request $request)
    {
        $subs = ExpenseCategory::where('parent_category', $request->parent)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json($subs);
    }
}
