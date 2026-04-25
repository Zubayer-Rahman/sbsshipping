<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * List suppliers or clients
     */
    public function index(string $type = 'supplier')
    {
        $type = in_array($type, ['supplier', 'client']) ? $type : 'supplier';

        $contacts = Contact::query()
            ->when($type === 'supplier', fn($q) => $q->suppliers())
            ->when($type === 'client', fn($q) => $q->clients())
            ->latest()
            ->get();

        return view('contacts.index', compact('contacts', 'type'));
    }

    /**
     * Create form
     */
    public function create(Request $request)
    {
        $type = $request->query('type', 'supplier');
        return view('contacts.create', compact('type'));
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $request->validate([
            'type'            => 'required|in:supplier,client,both',
            'business_name'   => 'nullable|string|max:255',
            'name'            => 'nullable|string|max:255',
            'email'           => 'nullable|email|max:255',
            'tax_number'      => 'nullable|string|max:100',
            'pay_term_number' => 'nullable|integer|min:1',
            'pay_term_type'   => 'nullable|in:days,months',
            'opening_balance' => 'nullable|numeric|min:0',
            'advance_balance' => 'nullable|numeric|min:0',
            'address'         => 'nullable|string|max:1000',
            'mobile'          => 'nullable|string|max:20',
        ]);

        Contact::create([
            'contact_id'      => Contact::generateContactId(),
            'type'            => $request->type,
            'business_name'   => $request->business_name,
            'name'            => $request->name,
            'email'           => $request->email,
            'tax_number'      => $request->tax_number,
            'pay_term_number' => $request->pay_term_number,
            'pay_term_type'   => $request->pay_term_type,
            'opening_balance' => $request->opening_balance ?? 0,
            'advance_balance' => $request->advance_balance ?? 0,
            'address'         => $request->address,
            'mobile'          => $request->mobile,
            'user_id'         => Auth::id(),
        ]);

        $label = $request->type === 'client' ? 'Client' : 'Supplier';

        if ($request->action === 'save_and_add') {
            return redirect()->route('contacts.create', ['type' => $request->type])
                ->with('success', "{$label} created! Add another.");
        }

        return redirect()->route('contacts.index', $request->type)
            ->with('success', "{$label} created successfully.");
    }

    /**
     * Show
     */
    public function show(Contact $contact)
    {
        return view('contacts.show', compact('contact'));
    }

    /**
     * Edit form
     */
    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }

    /**
     * Update
     */
    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'type'            => 'required|in:supplier,client,both',
            'business_name'   => 'nullable|string|max:255',
            'name'            => 'nullable|string|max:255',
            'email'           => 'nullable|email|max:255',
            'tax_number'      => 'nullable|string|max:100',
            'pay_term_number' => 'nullable|integer|min:1',
            'pay_term_type'   => 'nullable|in:days,months',
            'opening_balance' => 'nullable|numeric|min:0',
            'advance_balance' => 'nullable|numeric|min:0',
            'address'         => 'nullable|string|max:1000',
            'mobile'          => 'nullable|string|max:20',
        ]);

        $contact->update($request->only([
            'type', 'business_name', 'name', 'email', 'tax_number',
            'pay_term_number', 'pay_term_type', 'opening_balance',
            'advance_balance', 'address', 'mobile',
        ]));

        $label = $contact->type === 'client' ? 'Client' : 'Supplier';

        return redirect()->route('contacts.index', $contact->type)
            ->with('success', "{$label} updated successfully.");
    }

    /**
     * Delete
     */
    public function destroy(Contact $contact)
    {
        $type = $contact->type;
        $contact->delete();

        return redirect()->route('contacts.index', $type)
            ->with('success', 'Contact deleted successfully.');
    }

    /**
     * Toggle active/inactive
     */
    public function toggleActive(Contact $contact)
    {
        $contact->update(['is_active' => !$contact->is_active]);
        $status = $contact->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Contact {$status}.");
    }
}