<?php

namespace App\Http\Controllers;

use App\Models\JobGroup;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobGroupController extends Controller
{
    public function index(Request $request)
    {
        $query = JobGroup::withCount('jobs')->with('creator');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('group_code', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $groups = $query->latest()->paginate(20);

        return view('job_groups.index', compact('groups'));
    }

    public function create()
    {
        $jobs = Job::orderBy('id', 'desc')->get();
        $groupCode = JobGroup::generateGroupCode();
        return view('job_groups.create', compact('jobs', 'groupCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,completed,archived',
            'job_ids' => 'nullable|array',
            'job_ids.*' => 'exists:sbs_jobs,id',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $group = JobGroup::create([
                'name' => $validated['name'],
                'group_code' => JobGroup::generateGroupCode(),
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'created_by' => Auth::id(),
            ]);

            if (!empty($validated['job_ids'])) {
                $group->jobs()->sync($validated['job_ids']);
            }
        });

        return redirect()->route('job-groups.index')
            ->with('success', 'Job Group created successfully!');
    }

    public function show(JobGroup $jobGroup)
    {
        $jobGroup->load(['jobs.user', 'creator']);
        return view('job_groups.show', compact('jobGroup'));
    }

    public function edit(JobGroup $jobGroup)
    {
        $jobs = Job::orderBy('id', 'desc')->get();
        $selectedJobIds = $jobGroup->jobs->pluck('id')->toArray();
        return view('job_groups.edit', compact('jobGroup', 'jobs', 'selectedJobIds'));
    }

    public function update(Request $request, JobGroup $jobGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,completed,archived',
            'job_ids' => 'nullable|array',
            'job_ids.*' => 'exists:sbs_jobs,id',
        ]);

        DB::transaction(function () use ($validated, $jobGroup) {
            $jobGroup->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
            ]);

            $jobGroup->jobs()->sync($validated['job_ids'] ?? []);
        });

        return redirect()->route('job-groups.index')->with('success', 'Updated!');
    }

    public function destroy(JobGroup $jobGroup)
    {
        $jobGroup->delete();
        return redirect()->route('job-groups.index')->with('success', 'Deleted!');
    }

    // ★ AJAX endpoint for Job Create form (quick add new group)
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:job_groups,name',
        ]);

        $group = JobGroup::create([
            'name' => $validated['name'],
            'group_code' => JobGroup::generateGroupCode(),
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
                'group_code' => $group->group_code,
            ]
        ]);
    }
}
