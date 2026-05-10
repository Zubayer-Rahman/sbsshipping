<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PaymentAccount;

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
        $contacts   = Contact::where('is_active', true)->whereIn('type', ['client', 'both'])->orderBy('business_name')->get();
        $jobs       = DB::table('sbs_jobs')->orderByDesc('id')->get(['id', 'job_no', 'job_id']);
        $categories = ExpenseCategory::orderBy('parent_category')->orderBy('name')->get();

        return view('expenses.create', compact('users', 'contacts', 'jobs', 'categories'));
    }

    // ── Store ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        // Validate including the payment account
        $validated = $request->validate([
            'payment_account_id' => 'required|exists:payment_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'expense_category' => 'required|string|max:255',
            'sub_category' => 'nullable|string|max:255',
            'expenses_for' => 'nullable|string|max:255',
            'contact_id' => 'nullable|exists:contacts,id',
            'job_id' => 'nullable|exists:sbs_jobs,id',
            'document' => 'nullable|file|max:2048', // Max 2MB
        ]);

        $data = $request->except('_token', 'job_ids', 'payment_account_id'); // Exclude payment_account_id from mass assignment

        // Handle multiple job selections
        $account = PaymentAccount::find($validated['payment_account_id']);

        if ($validated['amount'] > $account->current_balance) {
            return redirect()->back()
                ->with('error', "Cannot create expense. {$account->account_name} balance is too low (৳" . number_format($account->current_balance, 2) . ")")
                ->withInput();
        }


        $jobIds = array_filter((array) $request->input('job_ids', []));
        $data['job_id']     = !empty($jobIds) ? $jobIds[0] : null;
        $data['job_ref_no'] = $request->input('job_ref_no');

        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')
                ->store('expense_docs', 'public');
        }

        $data['user_id']      = Auth::id();
        $data['added_by']     = Auth::user()->name;
        $data['is_refund']    = $request->boolean('is_refund');
        $data['is_recurring'] = $request->boolean('is_recurring');

        // Clean empty values
        foreach ($data as $k => $v) {
            if ($v === '') $data[$k] = null;
        }

        // Use DB Transaction to ensure both expense and account update together
        DB::transaction(function () use ($data, $request, $account) {
            $expense = Expense::create($data);

            $account = \App\Models\PaymentAccount::find($request->payment_account_id);
            $account->recordTransaction(
                'debit', // Expenses are always money out
                $data['amount'],
                'expense',
                $expense->id,
                "Expense: " . ($data['expenses_for'] ?? 'General'),
                $data['expense_date'],
                Auth::id()
            );
        });

        // 4. Atomic Database Transaction
        DB::transaction(function () use ($request, $account) {
            // Create the Expense
            $expense = \App\Models\Expense::create([
                'amount' => $request->amount,
                'expense_date' => $request->expense_date,
                'expenses_for' => $request->expenses_for,
                'payment_account_id' => $request->payment_account_id, // Store for audit
                'user_id' => Auth::id(),
                // ... other fields
            ]);

            // Real-time Deduction from selected account
            $account->recordTransaction(
                'debit', // Money OUT
                $request->amount,
                'expense',
                $expense->id,
                "Expense: " . $request->expenses_for,
                $request->expense_date,
                Auth::id()
            );
        });

        return redirect()->route('expenses.list')
            ->with('success', 'Expense added successfully and account updated!');
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
