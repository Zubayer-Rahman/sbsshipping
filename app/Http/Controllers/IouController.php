<?php

namespace App\Http\Controllers;

use App\Models\Iou;
use App\Models\IouPayment;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
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
        $jobs = DB::table('sbs_jobs')->orderByDesc('id')->get(['id', 'job_no', 'job_id']);
        $contacts = Contact::orderBy('name')->get();
        $referenceNumber = Iou::generateReferenceNumber();

        return view('ious.create', compact('jobs', 'contacts', 'referenceNumber'));
    }

    // Store new IOU
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'job_id' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:receivable,payable',
            'against' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $validated['reference_number'] = Iou::generateReferenceNumber();
        $validated['balance'] = $validated['amount'];
        $validated['created_by'] = Auth::id();

        if ($request->hasFile('document')) {
            $validated['document'] = $request->file('document')->store('iou_docs', 'public');
        }

        $jobIdsString = $request->job_id;
        unset($validated['job_id']);

        $iou = Iou::create($validated);

        if (!empty($jobIdsString)) {
            // Convert "1,2,13" into array [1, 2, 13]
            $jobIdsArray = explode(',', $jobIdsString);
            // Use sync() to save them to the iou_job table
            $iou->jobs()->sync($jobIdsArray);
        }

        return redirect()->route('ious.show', $iou)
            ->with('success', 'IOU created successfully!');
    }

    // Show single IOU
    public function show(Iou $iou)
    {
        $iou->load(['contact', 'jobs', 'creator', 'payments.job', 'payments.client']);
        $jobs = Job::orderBy('id', 'desc')->get();
        $clients = Contact::orderBy('name')->get(); // Adjust if you have a specific 'client' type

        return view('ious.show', compact('iou', 'jobs', 'clients'));
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
            'job_id' => 'nullable|exists:sbs_jobs,id',
            'client_id' => 'nullable|exists:contacts,id',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['iou_id'] = $iou->id;
        $validated['created_by'] = Auth::id();

        IouPayment::create($validated);

        // This updates paid_amount, balance, and Status (Partial/Paid)
        $iou->updateBalance();

        return redirect()->back()->with('success', 'Payment recorded as IOU Expense!');
    }

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
