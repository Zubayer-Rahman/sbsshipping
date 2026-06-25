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
        $query = Expense::with('jobs')->latest(); // ✅ Added eager loading

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


    public function additionalExpenses(Request $request)
    {
        $query = \App\Models\BillAdditionalExpense::with(['bill.client', 'job']);

        // Optional filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('description', 'like', "%{$search}%")
                ->orWhereHas('bill', function ($q) use ($search) {
                    $q->where('bill_no', 'like', "%{$search}%")
                        ->orWhere('client_name', 'like', "%{$search}%");
                });
        }

        if ($request->filled('type')) {
            if ($request->type === 'auto') {
                $query->where('is_auto', true);
            } elseif ($request->type === 'manual') {
                $query->where('is_auto', false);
            }
        }

        $expenses = $query->latest()->paginate(20);

        // Stats
        $totalAuto = \App\Models\BillAdditionalExpense::where('is_auto', true)->sum('amount');
        $totalManual = \App\Models\BillAdditionalExpense::where('is_auto', false)->sum('amount');
        $totalAll = $totalAuto + $totalManual;

        return view('expenses.additionalExpenses', compact('expenses', 'totalAuto', 'totalManual', 'totalAll'));
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
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
        $request->validate([
            'total_amount'       => 'required|numeric|min:0',
            'expense_date'       => 'required',
            'payment_account_id' => 'required|exists:payment_accounts,id',
            'job_ids'            => 'nullable|array',
            'job_ids.*'          => 'exists:sbs_jobs,id',
        ]);

        $account = PaymentAccount::find($request->payment_account_id);

        try {
            DB::transaction(function () use ($request, $account) {
                $data = $request->except('_token', 'job_ids', 'job_ref_no');

                if ($request->hasFile('document')) {
                    $data['document_path'] = $request->file('document')
                        ->store('expense_docs', 'public');
                }

                $data['user_id']      = Auth::id();
                $data['added_by']     = Auth::user()->name;
                $data['is_refund']    = $request->boolean('is_refund');
                $data['is_recurring'] = $request->boolean('is_recurring');
                $data['payment_account_id'] = $request->payment_account_id;

                foreach ($data as $k => $v) {
                    if ($v === '') $data[$k] = null;
                }

                // ✅ Create the Expense
                $expense = \App\Models\Expense::create($data);

                // ✅ Verify the expense was created with an ID
                if (!$expense->id) {
                    throw new \Exception('Failed to create expense - no ID returned');
                }

                // ✅ Attach to jobs via pivot table
                $jobIds = array_filter((array) $request->input('job_ids', []));

                if (!empty($jobIds)) {
                    foreach ($jobIds as $jobId) {
                        // Direct creation to avoid the null issue
                        \App\Models\ExpenseJob::firstOrCreate([
                            'expense_id' => $expense->id,
                            'job_id'     => $jobId,
                        ]);
                    }
                }

                // REAL-TIME DEDUCTION
                $account->recordTransaction(
                    'debit',
                    $request->total_amount,
                    'expense',
                    $expense->id,
                    "Expense: " . ($request->expense_for ?? 'Business Expense'),
                    $request->expense_date ?? now()->toDateString(),
                    Auth::id()
                );
            });

            return redirect()->route('expenses.list')
                ->with('success', 'Expense added and balance deducted!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
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
        $request->validate([
            'total_amount'       => 'required|numeric|min:0',
            'expense_date'       => 'required',
            'payment_account_id' => 'required|exists:payment_accounts,id',
            'job_ids'            => 'nullable|array',
            'job_ids.*'          => 'exists:jobs,id',
        ]);

        try {
            DB::transaction(function () use ($request, $expense) {
                $data = $request->except('_token', '_method', 'job_ids', 'job_ref_no');

                if ($request->hasFile('document')) {
                    $data['document_path'] = $request->file('document')
                        ->store('expense_docs', 'public');
                }

                foreach ($data as $k => $v) {
                    if ($v === '') $data[$k] = null;
                }

                $data['is_refund']    = $request->boolean('is_refund');
                $data['is_recurring'] = $request->boolean('is_recurring');

                $total = floatval($data['total_amount'] ?? $expense->total_amount);
                $paid  = floatval($data['payment_amount'] ?? $expense->payment_amount);
                $due   = $total - $paid;
                $data['payment_due']    = max(0, $due);
                $data['payment_status'] = $due <= 0 ? 'Paid' : ($paid > 0 ? 'Partial' : 'Due');

                $expense->update($data);

                // ✅ Sync pivot table
                $jobIds = array_filter((array) $request->input('job_ids', []));

                \App\Models\ExpenseJob::where('expense_id', $expense->id)->delete();

                if (!empty($jobIds)) {
                    foreach ($jobIds as $jobId) {
                        \App\Models\ExpenseJob::create([
                            'expense_id' => $expense->id,
                            'job_id'     => $jobId,
                        ]);
                    }
                }
            });

            return redirect()->route('expenses.list')
                ->with('success', 'Expense updated.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            DB::transaction(function () use ($expense) {
                \App\Models\ExpenseJob::where('expense_id', $expense->id)->delete();
                $expense->delete();
            });

            return back()->with('success', 'Expense deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting expense: ' . $e->getMessage());
        }
    }

    // ── AJAX: subcategories for a parent ─────────────────────────────────────
    public function subcategories(Request $request)
    {
        $parent = trim($request->parent);

        // Case-insensitive search
        $subs = ExpenseCategory::whereRaw('LOWER(TRIM(parent_category)) = ?', [strtolower($parent)])
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'parent_category']);

        return response()->json($subs);
    }
}
