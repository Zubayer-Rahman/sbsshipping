<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Contact;
use App\Models\User;
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
        if ($request->boolean('today')) {
            $query->whereDate('created_at', today());
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

        $users = User::orderBy('name')->get();
        // Load all active clients from the contacts table
        $clients = Contact::where('type', 'client')
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get();

        $types = Job::whereNotNull('type')->distinct()->pluck('type')->sort()->values();

        return view('jobs.create', compact('clients', 'types', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token', 'job_group_id');
        $data['user_id'] = Auth::id();

        foreach ($data as $key => $value) {
            if ($value === '') $data[$key] = null;
        }

        $job = Job::create($data);

        if ($request->filled('job_group_id')) {
            $job->jobGroups()->attach($request->job_group_id);
        }

        return redirect()->route('jobs.list')
            ->with('success', 'Job created successfully!');
    }

    public function update(Request $request, Job $job)
    {
        $data = $request->except('_token', '_method', 'job_group_id');
        foreach ($data as $key => $value) {
            if ($value === '') $data[$key] = null;
        }
        $job->update($data);

        if ($request->filled('job_group_id')) {
            $job->jobGroups()->sync([$request->job_group_id]);
        } else {
            $job->jobGroups()->detach();
        }

        return redirect()->route('jobs.list')
            ->with('success', 'Job updated successfully!');
    }

    public function show(Job $job)
    {
        return view('jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        $clients = Contact::where('type', 'client')->get();
        $users = User::orderBy('name')->get();
        return view('jobs.edit', compact('job', 'clients', 'users'));
    }


    public function print(Job $job)
    {
        $jobs = collect([$job]);
        return view('jobs.print', compact('job', 'jobs'));
    }

    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('jobs.list')->with('success', 'Job deleted.');
    }
}
