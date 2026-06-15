<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::latest('id');

        if ($request->filled('client')) {
            $query->where('client_name', $request->client);
        }
        if ($request->filled('job_bill')) {
            $query->where('job_no', $request->job_bill);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('receive_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('receive_date', '<=', $request->date_to);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $jobs    = $query->paginate(20);
        $clients = Job::whereNotNull('client_name')->distinct()->pluck('client_name')->sort()->values();
        $jobNos  = Job::whereNotNull('job_no')->distinct()->pluck('job_no')->sortDesc()->values();
        $types   = Job::whereNotNull('type')->distinct()->pluck('type')->sort()->values();

        return view('jobs.list', compact('jobs', 'clients', 'jobNos', 'types'));
    }

    public function create()
    {
        // Load all active clients from the contacts table
        $clients = Contact::where('type', 'client')
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get();

        $types = Job::whereNotNull('type')->distinct()->pluck('type')->sort()->values();

        return view('jobs.create', compact('clients', 'types'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        $data['user_id'] = Auth::id();

        foreach ($data as $key => $value) {
            if ($value === '') $data[$key] = null;
        }

        $job = Job::create($data);

        // Attach to job group if selected
        if ($request->job_group_id) {
            $job->groups()->attach($request->job_group_id);
        }

        return redirect()->route('jobs.list')
            ->with('success', 'Job created successfully!');
    }

    public function show(Job $job)
    {
        return view('jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        $clients = Contact::where('type', 'client')
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get();

        $types = Job::whereNotNull('type')->distinct()->pluck('type')->sort()->values();

        return view('jobs.edit', compact('job', 'clients', 'types' ));
    }

    public function update(Request $request, Job $job)
    {
        $data = $request->except('_token', '_method');
        foreach ($data as $key => $value) {
            if ($value === '') $data[$key] = null;
        }
        $job->update($data);

        if ($request->job_group_id) {
            $job->groups()->sync([$request->job_group_id]);
        } else {
            $job->groups()->detach();
        }

        return redirect()->route('jobs.list')
            ->with('success', 'Job updated successfully!');
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('jobs.list')->with('success', 'Job deleted.');
    }
}
