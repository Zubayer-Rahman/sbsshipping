<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    // Jobs List
    public function index(Request $request)
    {
        $query = Job::latest();

        // Search / Filter
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('job_id', 'like', "%{$s}%")
                    ->orWhere('client_name', 'like', "%{$s}%")
                    ->orWhere('origin', 'like', "%{$s}%")
                    ->orWhere('destination', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jobs = $query->paginate(15);
        return view('jobs.list', compact('jobs'));
    }

    // Create Job Form
    public function create()
    {
        return view('jobs.create');
    }

    // Store Job
    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Clean up empty strings to null
        foreach ($data as $key => $value) {
            if ($value === '') $data[$key] = null;
        }

        Job::create($data);

        return redirect()->route('jobs.list')
            ->with('success', 'Job created successfully!');
    }

    // Show single job
    public function show(Job $job)
    {
        return view('jobs.show', compact('job'));
    }

    // Edit job
    public function edit(Job $job)
    {
        return view('jobs.edit', compact('job'));
    }

    // Update job
    public function update(Request $request, Job $job)
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            if ($value === '') $data[$key] = null;
        }
        $job->update($data);

        return redirect()->route('jobs.list')
            ->with('success', 'Job updated successfully!');
    }

    // Delete job
    public function destroy(Job $job)
    {
        $job->delete();
        return redirect()->route('jobs.list')
            ->with('success', 'Job deleted.');
    }

    // Forwarding page (bill generation placeholder)
    public function forwarding()
    {
        $jobs = Job::latest()->get();
        return view('jobs.forwarding', compact('jobs'));
    }
}
