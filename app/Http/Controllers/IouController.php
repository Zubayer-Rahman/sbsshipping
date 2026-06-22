<?php

namespace App\Http\Controllers;

use App\Models\Iou;
use App\Models\IouPayment;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentAccount;
use App\Models\Job;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IouController extends Controller
{
    // List all IOUs
    public function index(Request $request)
    {
        $query = Iou::with(['contact', 'creator']);

        // Filter by type
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('contact', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $ious = $query->latest()->paginate(20);

        // Calculate totals
        $totalReceivable = Iou::where('type', 'receivable')
            ->where('status', '!=', 'paid')
            ->sum('balance');
        $totalPayable = Iou::where('type', 'payable')
            ->where('status', '!=', 'paid')
            ->sum('balance');

        return view('ious.index', compact('ious', 'totalReceivable', 'totalPayable'));
    }

    // Show create form
    public function create()
    {
        $users = User::orderBy('name')->get();
        $jobs = DB::table('sbs_jobs')->orderByDesc('id')->get(['id', 'job_no', 'job_id']);
        $contacts = Contact::orderBy('name')->get();
        $referenceNumber = Iou::generateReferenceNumber();

        return view('ious.create', compact('jobs', 'contacts', 'referenceNumber', 'users'));
    }

    // Store new IOU
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:users,id',
            // 'user_id' => 'nullable|exists:users,id',
            'job_id' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:receivable,payable',
            'payment_account_id' => 'nullable|exists:payment_accounts,id',
            'against' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // // Balance check ONLY if account is selected AND it's a Receivable IOU
        // if (!empty($validated['payment_account_id']) && $validated['type'] == 'receivable') {
        //     $account = PaymentAccount::find($validated['payment_account_id']);

        //     // Check if account name contains "cash" (Cash in Hand can go negative)
        //     $isCashAccount = $account && stripos($account->account_name, 'cash') !== false;

        //     if ($account && !$isCashAccount && $validated['amount'] > $account->current_balance) {
        //         return redirect()->back()
        //             ->with('error', "Insufficient funds in {$account->account_name}! Only Cash in Hand can go negative.")
        //             ->withInput();
        //     }
        // }

        return DB::transaction(function () use ($request, $validated) {
            $validated['reference_number'] = Iou::generateReferenceNumber();
            $validated['balance'] = $validated['amount'];
            $validated['created_by'] = Auth::id();

            if ($request->hasFile('document')) {
                $validated['document'] = $request->file('document')->store('iou_docs', 'public');
            }

            $jobIdsString = $request->job_id;
            unset($validated['job_id']);

            // 1. Create the IOU
            $iou = Iou::create($validated);

            // 2. Link Jobs to pivot table
            if (!empty($jobIdsString)) {
                $iou->jobs()->sync(explode(',', $jobIdsString));
            }

            // 3. ★★★ ONLY UPDATE ACCOUNT IF ONE WAS SELECTED ★★★
            if (!empty($validated['payment_account_id'])) {
                $account = PaymentAccount::find($validated['payment_account_id']);

                if ($account) {
                    // Receivable = Money OUT (Debit), Payable = Money IN (Credit)
                    $transType = ($validated['type'] == 'receivable') ? 'debit' : 'credit';
                    $desc = ($validated['type'] == 'receivable')
                        ? "Issued IOU: " . $iou->reference_number
                        : "Received funds via IOU: " . $iou->reference_number;

                    $account->recordTransaction(
                        $transType,
                        $validated['amount'],
                        'iou_creation',
                        $iou->id,
                        $desc . " to " . $iou->contact->name,
                        now(),
                        Auth::id()
                    );
                }
            }

            return redirect()->route('ious.show', $iou)
                ->with('success', 'IOU created successfully!');
        });
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'contact_id' => 'required|exists:contacts,id',
    //         'job_id' => 'nullable|string',
    //         'amount' => 'required|numeric|min:0.01',
    //         'type' => 'required|in:receivable,payable',
    //         'against' => 'nullable|string|max:255',
    //         'description' => 'nullable|string',
    //         'due_date' => 'nullable|date',
    //         'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    //         'payment_account_id' => 'nullable|exists:payment_accounts,id',
    //     ]);

    //     // Only check balance if account was selected AND it's a Receivable IOU
    //     // if (!empty($validated['payment_account_id']) && $validated['type'] == 'receivable') {
    //     //     $account = PaymentAccount::find($validated['payment_account_id']);

    //     //     // ★★★ CHECK: Allow Cash in Hand to go negative ★★★
    //     //     $isCashAccount = stripos($account->account_name, 'cash') !== false
    //     //         || stripos($account->account_type, 'cash') !== false;

    //     //     // Only block if NOT a cash account AND insufficient balance
    //     //     if (!$isCashAccount && $validated['amount'] > $account->current_balance) {
    //     //         return redirect()->back()
    //     //             ->with('error', "Insufficient funds in {$account->account_name}! Cash in Hand can go negative, but Bank accounts cannot.")
    //     //             ->withInput();
    //     //     }
    //     // }

    //     // Only check balance if an account was selected
    //     if (!empty($validated['payment_account_id'])) {
    //         $account = PaymentAccount::find($validated['payment_account_id']);

    //         if ($account && $validated['type'] == 'receivable' && $validated['amount'] > $account->current_balance) {
    //             return redirect()->back()
    //                 ->with('error', "Insufficient funds in {$account->account_name}!")
    //                 ->withInput();
    //         }
    //     }

    //     return DB::transaction(function () use ($request, $validated, $account) {

    //         $validated['reference_number'] = Iou::generateReferenceNumber();
    //         $validated['balance'] = $validated['amount'];
    //         $validated['created_by'] = Auth::id();

    //         if ($request->hasFile('document')) {
    //             $validated['document'] = $request->file('document')->store('iou_docs', 'public');
    //         }

    //         $jobIdsString = $request->job_id;
    //         unset($validated['job_id']);

    //         // Create the IOU
    //         $iou = Iou::create($validated);

    //         // Link Jobs
    //         if (!empty($jobIdsString)) {
    //             $iou->jobs()->sync(explode(',', $jobIdsString));
    //         }

    //         // 4. REAL-TIME ACCOUNT UPDATE
    //         // If Receivable: Business GIVES money out (Debit)
    //         // If Payable: Business TAKES money in (Credit - e.g. a loan from someone)
    //         $transType = ($validated['type'] == 'receivable') ? 'debit' : 'credit';
    //         $desc = ($validated['type'] == 'receivable')
    //             ? "Issued IOU to {$iou->contact->name}"
    //             : "Received funds via IOU from {$iou->contact->name}";

    //         $account->recordTransaction(
    //             $transType,
    //             $validated['amount'],
    //             'iou_creation',
    //             $iou->id,
    //             $desc . " ({$iou->reference_number})",
    //             now(),
    //             Auth::id()
    //         );

    //         return redirect()->route('ious.show', $iou)
    //             ->with('success', 'IOU created and Account balance updated!');
    //     });
    // }



    // Show single IOU
    public function show(Iou $iou)
    {
        $iou->load(['contact', 'jobs', 'creator', 'payments.job', 'payments.client']);
        $jobs = Job::orderBy('id', 'desc')->get();
        $clients = Contact::orderBy('name')->get(); // Adjust if you have a specific 'client' type


        // fetch active payment accounts for the payment form dropdown
        $accounts = PaymentAccount::where('is_active', true)->get();

        return view('ious.show', compact('iou', 'jobs', 'clients', 'accounts'));
    }

    // Show edit form
    public function edit(Iou $iou)
    {
        if ($iou->status == 'paid') {
            return redirect()->route('ious.show', $iou)
                ->with('error', 'Cannot edit paid IOU');
        }

        $contacts = Contact::orderBy('name')->get();
        return view('ious.edit', compact('iou', 'contacts'));
    }

    // Update IOU
    public function update(Request $request, Iou $iou)
    {
        if ($iou->status == 'paid') {
            return redirect()->route('ious.show', $iou)
                ->with('error', 'Cannot edit paid IOU');
        }

        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:receivable,payable',
            'against' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Recalculate balance
        $validated['balance'] = $validated['amount'] - $iou->paid_amount;

        if ($request->hasFile('document')) {
            // Delete old document
            if ($iou->document) {
                Storage::disk('public')->delete($iou->document);
            }
            $validated['document'] = $request->file('document')->store('iou_docs', 'public');
        }

        $iou->update($validated);

        return redirect()->route('ious.show', $iou)
            ->with('success', 'IOU updated successfully!');
    }

    // Add payment

    public function addPayment(Request $request, Iou $iou)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_account_id' => 'nullable|exists:payment_accounts,id',
            'job_id' => 'nullable|exists:sbs_jobs,id',
            'client_id' => 'nullable|exists:contacts,id',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Only check balance if account was selected and IOU is payable
        if (!empty($validated['payment_account_id'])) {
            $account = PaymentAccount::find($validated['payment_account_id']);

            if ($account && $iou->type == 'payable' && $validated['amount'] > $account->current_balance) {
                return redirect()->back()
                    ->with('error', "Insufficient funds in {$account->account_name}!")
                    ->withInput();
            }
        }

        DB::transaction(function () use ($validated, $iou) {
            // 1. Create the payment record
            $payment = IouPayment::create([
                'iou_id' => $iou->id,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'job_id' => $validated['job_id'] ?? null,
                'client_id' => $validated['client_id'] ?? null,
                'payment_method' => $validated['payment_method'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // 2. Update IOU balance/status
            $iou->updateBalance();

            // 3. ONLY UPDATE ACCOUNT IF ONE WAS SELECTED
            if (!empty($validated['payment_account_id'])) {
                $account = PaymentAccount::find($validated['payment_account_id']);

                if ($account) {
                    $type = ($iou->type == 'receivable') ? 'credit' : 'debit';

                    $account->recordTransaction(
                        $type,
                        $validated['amount'],
                        'iou_payment',
                        $payment->id,
                        "IOU Payment for {$iou->reference_number}",
                        $validated['payment_date'],
                        Auth::id()
                    );
                }
            }
        });

        return redirect()->back()->with('success', 'Payment recorded successfully!');
    }
    // public function addPayment(Request $request, Iou $iou)
    // {
    //     $validated = $request->validate([
    //         'amount' => 'required|numeric|min:0.01',
    //         'payment_date' => 'required|date',
    //         'payment_account_id' => 'nullable|exists:payment_accounts,id',
    //         'job_id' => 'nullable|exists:sbs_jobs,id',
    //         'client_id' => 'nullable|exists:contacts,id',
    //         'payment_method' => 'nullable|string',
    //         'notes' => 'nullable|string',
    //     ]);
    //     $account = PaymentAccount::find($validated['payment_account_id']);
    //     $validated['iou_id'] = $iou->id;
    //     $validated['created_by'] = Auth::id();


    //     if ($iou->type == 'payable' && $validated['amount'] > $account->current_balance) {
    //         return redirect()->back()
    //             ->with('error', "Insufficient funds! {$account->account_name} only has ৳" . number_format($account->current_balance, 2))
    //             ->withInput();
    //     }

    //     DB::transaction(function () use ($validated, $iou, $account) {
    //         // 1. Create the payment record 
    //         // We use ?? null to prevent "Undefined array key" errors
    //         $payment = IouPayment::create([
    //             'iou_id'         => $iou->id,
    //             'amount'         => $validated['amount'],
    //             'payment_date'   => $validated['payment_date'],
    //             'job_id'         => $validated['job_id'] ?? null,
    //             'client_id'      => $validated['client_id'] ?? null,
    //             'payment_method' => $validated['payment_method'] ?? null,
    //             'notes'          => $validated['notes'] ?? null,          
    //             'created_by'     => Auth::id(),
    //         ]);

    //         // 2. Update IOU balance and status
    //         $iou->updateBalance();

    //         if (!empty($validated['payment_account_id'])) {
    //             $account = PaymentAccount::find($validated['payment_account_id']);
    //             $type = ($iou->type == 'receivable') ? 'credit' : 'debit';

    //             $account->recordTransaction(
    //                 $type,
    //                 $validated['amount'],
    //                 'iou_payment',
    //                 $payment->id,
    //                 "IOU Payment for {$iou->reference_number}",
    //                 $validated['payment_date'],
    //                 Auth::id()
    //             );
    //         } else {
    //             // If no payment account is selected, we still want to record the transaction for tracking purposes
    //             $type = ($iou->type == 'receivable') ? 'credit' : 'debit';
    //         }

    //         $account->recordTransaction(
    //             $type,
    //             $validated['amount'],
    //             'iou_payment',
    //             $payment->id,
    //             "IOU Payment: {$iou->reference_number}",
    //             $validated['payment_date'],
    //             Auth::id()
    //         );
    //     });

    //     return redirect()->back()->with('success', 'Payment recorded Successfully!');
    // }

    // List of IOU payments for expenses page
    public function iouExpenseList()
    {
        $payments = IouPayment::with(['iou.contact', 'job', 'client', 'creator'])
            ->latest('payment_date')
            ->paginate(20);

        return view('ious.expense-list', compact('payments'));
    }

    // Delete IOU
    public function destroy(Iou $iou)
    {
        if ($iou->payments()->count() > 0) {
            return redirect()->route('ious.index')
                ->with('error', 'Cannot delete IOU with payments. Delete payments first.');
        }

        if ($iou->document) {
            Storage::disk('public')->delete($iou->document);
        }

        $iou->delete();

        return redirect()->route('ious.index')
            ->with('success', 'IOU deleted successfully!');
    }


    // Show release form
    public function release(Iou $iou)
    {
        if ($iou->is_released) {
            return redirect()->route('ious.show', $iou)
                ->with('error', 'This IOU has already been released.');
        }

        $categories = ExpenseCategory::orderBy('name')->get();
        $jobs = Job::orderBy('job_id', 'desc')->get();

        return view('ious.release', compact('iou', 'categories', 'jobs'));
    }

    // Process the release
    public function processRelease(Request $request, Iou $iou)
    {
        if ($iou->is_released) {
            return redirect()->route('ious.show', $iou)
                ->with('error', 'This IOU has already been released.');
        }

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'sub_category' => 'nullable|string|max:255',
            'job_id' => 'nullable|exists:sbs_jobs,id',
            'expense_date' => 'required|date',
            'expenses_for' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Create expense entry
            $expenseData = [
                'expense_category_id' => $validated['expense_category_id'],
                'sub_category' => $validated['sub_category'],
                'job_id' => $validated['job_id'],
                'expense_date' => $validated['expense_date'],
                'contact_id' => $iou->contact_id, // Auto-filled from IOU
                'expenses_for' => $validated['expenses_for'],
                'client_name' => $validated['client_name'],
                'amount' => $validated['amount'],
                'payment_method' => 'IOU Release',
                'notes' => "Released from IOU: {$iou->reference_number} ({$iou->type})",
                'created_by' => Auth::id(),
            ];

            if ($request->hasFile('document')) {
                $expenseData['document'] = $request->file('document')->store('expense_docs', 'public');
            }

            $expense = Expense::create($expenseData);

            // Update IOU - mark as released and update paid amount
            $iou->update([
                'is_released' => true,
                'expense_id' => $expense->id,
                'released_at' => now(),
                'released_by' => Auth::id(),
                'paid_amount' => $iou->paid_amount + $validated['amount'], // Add to paid amount
                'balance' => $iou->amount - ($iou->paid_amount + $validated['amount']), // Recalculate balance
            ]);

            // Update status based on balance
            if ($iou->balance <= 0) {
                $iou->update(['status' => 'paid', 'paid_date' => now()]);
            } elseif ($iou->paid_amount > 0) {
                $iou->update(['status' => 'partial']);
            }

            DB::commit();

            return redirect()->route('ious.release-list')
                ->with('success', 'IOU released successfully and added to expenses!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error releasing IOU: ' . $e->getMessage())
                ->withInput();
        }
    }

    // List of released IOUs
    public function releaseList(Request $request)
    {
        $query = Iou::with(['contact', 'jobs', 'payments', 'releasedBy']) // Eager load everything
            ->where('is_released', true);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('contact', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $ious = $query->latest()->paginate(20);

        $releasedIous = $query->latest('released_at')->paginate(20);

        return view('ious.release-list', compact('releasedIous'));
    }

    public function releaseInstant(Iou $iou)
    {
        if ($iou->is_released) {
            return redirect()->back()->with('error', 'This IOU is already released.');
        }

        // Update the IOU status
        $iou->update([
            'is_released' => true,
            'released_at' => now(),
            'released_by' => Auth::id(),
            // We set status to paid because 'Releasing' is the final act
            'status' => 'paid',
            'paid_date' => $iou->paid_date ?? now(),
        ]);

        return redirect()->route('ious.release-list')->with('success', "IOU {$iou->reference_number} has been released successfully!");
    }
}
