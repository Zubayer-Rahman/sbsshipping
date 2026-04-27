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
        $contacts = Contact::orderBy('name')->get();
        $referenceNumber = Iou::generateReferenceNumber();

        return view('ious.create', compact('contacts', 'referenceNumber'));
    }

    // Store new IOU
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
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

        $iou = Iou::create($validated);

        return redirect()->route('ious.show', $iou)
            ->with('success', 'IOU created successfully!');
    }

    // Show single IOU
    public function show(Iou $iou)
    {
        $iou->load(['contact', 'creator', 'payments.creator']);
        return view('ious.show', compact('iou'));
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
            'amount' => 'required|numeric|min:0.01|max:' . $iou->balance,
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['iou_id'] = $iou->id;
        $validated['created_by'] = Auth::id();

        IouPayment::create($validated);
        $iou->updateBalance();

        return redirect()->route('ious.show', $iou)
            ->with('success', 'Payment added successfully!');
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
        $query = Iou::with(['contact', 'expense.category', 'releasedBy'])
            ->where('is_released', true);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('contact', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $releasedIous = $query->latest('released_at')->paginate(20);

        return view('ious.release-list', compact('releasedIous'));
    }
}
