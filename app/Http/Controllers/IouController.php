<?php

namespace App\Http\Controllers;

use App\Models\Iou;
use App\Models\IouPayment;
use App\Models\Contact;
use Illuminate\Http\Request;
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
}
