<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ForwardingLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ForwardingController extends Controller
{
    // ── Create / form page ───────────────────────────────────────────────────
    public function create()
    {
        $contacts = Contact::where('is_active', true)
            ->orderBy('business_name')
            ->get();

        return view('jobs.forwarding', compact('contacts'));
    }

    // ── AJAX: load jobs for a selected contact ───────────────────────────────
    public function jobsForContact(Request $request)
    {
        $contactId = $request->contact_id;
        $contact   = Contact::findOrFail($contactId);

        $jobs = DB::table('sbs_jobs')
            ->where('client_name', $contact->business_name)
            ->orderByDesc('id')
            ->get();

        return response()->json($jobs);
    }

    // ── Store letter + redirect to PDF preview ───────────────────────────────
    public function store(Request $request)
    {
        $selectedIds = array_filter((array) $request->input('selected_jobs', []));

        if (empty($selectedIds)) {
            return back()->withErrors(['selected_jobs' => 'Please select at least one job.']);
        }

        // Calculate total from selected jobs
        $total = DB::table('sbs_jobs')
            ->whereIn('id', $selectedIds)
            ->sum('invoice_value_usd');

        $letter = ForwardingLetter::create([
            'ref_no'           => $request->ref_no,
            'letter_date'      => $request->letter_date ?: now()->toDateString(),
            'subject'          => $request->subject,
            'contact_id'       => $request->contact_id,
            'selected_job_ids' => $selectedIds,
            'visible_columns'  => $request->input('visible_columns', ['job_no','be_no','ip_ep_no','ip_ep_date','be_no','awb_no','invoice_no','invoice_value_usd','buyer_name']),
            'bank_details'     => $request->bank_details,
            'total_amount'     => $total ?? 0,
            'user_id'          => Auth::id(),
        ]);

        return redirect()->route('forwarding.preview', $letter->id);
    }

    // ── PDF Preview page ─────────────────────────────────────────────────────
    public function preview(ForwardingLetter $letter)
    {
        $contact = Contact::find($letter->contact_id);
        $jobs    = $letter->jobs();
        return view('jobs.forwarding_preview', compact('letter', 'contact', 'jobs'));
    }

    // ── Forwarding List ──────────────────────────────────────────────────────
    public function index()
    {
        $letters = ForwardingLetter::with('contact')
            ->latest()
            ->paginate(20);

        return view('jobs.forwarding_list', compact('letters'));
    }

    // ── Delete ───────────────────────────────────────────────────────────────
    public function destroy(ForwardingLetter $letter)
    {
        $letter->delete();
        return back()->with('success', 'Forwarding letter deleted.');
    }
}