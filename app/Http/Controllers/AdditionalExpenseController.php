<?php

namespace App\Http\Controllers;

use App\Models\AdditionalExpense;
use App\Models\Contact;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdditionalExpenseController extends Controller
{
    // List all
    public function index(Request $request)
    {
        $query = AdditionalExpense::with(['client', 'job', 'creator', 'bill']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('reference_no', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q2) use ($search) {
                        $q2->where('business_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $expenses = $query->latest()->paginate(20);

        $totalActual = AdditionalExpense::where('status', '!=', 'cancelled')->sum('actual_amount');
        $totalToBill = AdditionalExpense::where('status', 'pending')->sum('to_be_billed');
        $totalBilled = AdditionalExpense::where('status', 'billed')->sum('to_be_billed');

        return view('additional_expenses.index', compact('expenses', 'totalActual', 'totalToBill', 'totalBilled'));
    }

    // Create form
    public function create()
    {
        $clients = Contact::orderBy('business_name')->get();
        $jobs = Job::orderBy('id', 'desc')->get();
        $referenceNo = AdditionalExpense::generateReferenceNo();

        return view('additional_expenses.create', compact('clients', 'jobs', 'referenceNo'));
    }

    // Store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:contacts,id',
            'job_id' => 'nullable|exists:sbs_jobs,id',
            'description' => 'required|string|max:255',
            'actual_amount' => 'required|numeric|min:0',
            'to_be_billed' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['reference_no'] = AdditionalExpense::generateReferenceNo();
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';

        AdditionalExpense::create($validated);

        return redirect()->route('additional-expenses.index')
            ->with('success', 'Additional expense created successfully!');
    }

    // Show
    public function show(AdditionalExpense $additionalExpense)
    {
        $additionalExpense->load(['client', 'job', 'creator', 'bill']);
        return view('additional_expenses.show', compact('additionalExpense'));
    }

    // Edit
    public function edit(AdditionalExpense $additionalExpense)
    {
        if ($additionalExpense->status === 'billed') {
            return redirect()->back()->with('error', 'Cannot edit an already billed expense.');
        }

        $clients = Contact::orderBy('business_name')->get();
        $jobs = Job::orderBy('id', 'desc')->get();

        return view('additional_expenses.edit', compact('additionalExpense', 'clients', 'jobs'));
    }

    // Update
    public function update(Request $request, AdditionalExpense $additionalExpense)
    {
        if ($additionalExpense->status === 'billed') {
            return redirect()->back()->with('error', 'Cannot edit an already billed expense.');
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:contacts,id',
            'job_id' => 'nullable|exists:sbs_jobs,id',
            'description' => 'required|string|max:255',
            'actual_amount' => 'required|numeric|min:0',
            'to_be_billed' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $additionalExpense->update($validated);

        return redirect()->route('additional-expenses.index')
            ->with('success', 'Additional expense updated successfully!');
    }

    // Delete
    public function destroy(AdditionalExpense $additionalExpense)
    {
        if ($additionalExpense->status === 'billed') {
            return redirect()->back()->with('error', 'Cannot delete an already billed expense.');
        }

        $additionalExpense->delete();
        return redirect()->route('additional-expenses.index')->with('success', 'Deleted!');
    }

    // ★★★ AJAX: Get pending expenses by job IDs (for Bill creation) ★★★
    public function getByJobs(Request $request)
    {
        $jobIds = explode(',', $request->job_ids);

        if (empty($jobIds) || (count($jobIds) === 1 && empty($jobIds[0]))) {
            return response()->json(['expenses' => []]);
        }

        $expenses = AdditionalExpense::whereIn('job_id', $jobIds)
            ->where('status', 'pending')
            ->with('job')
            ->get()
            ->map(function ($exp) {
                return [
                    'id' => $exp->id,
                    'reference_no' => $exp->reference_no,
                    'description' => $exp->description,
                    'to_be_billed' => $exp->to_be_billed,
                    'job_id' => $exp->job_id,
                    'job_label' => $exp->job ? ($exp->job->job_id ?? 'Job #' . $exp->job_id) : 'No Job',
                ];
            });

        return response()->json(['expenses' => $expenses]);
    }
}
