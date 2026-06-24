@extends('layouts.app')

@section('title', 'Edit Job Group')
@section('page-title', 'Edit Job Group')
@section('breadcrumb', 'Job Groups / Edit')

@section('content')

<div style="padding:2rem;max-width:900px;margin:0 auto;font-family:'Inter',sans-serif">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div>
            <h2 style="font-size:20px;font-weight:800;color:var(--text-primary);margin-bottom:4px">
                Edit Job Group
            </h2>
            <p style="font-size:13px;color:var(--text-muted)">{{ $jobGroup->group_code }}</p>
        </div>
        <a href="{{ route('job-groups.index') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Back to Groups
        </a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle-fill"></i>
        <div>
            @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('job-groups.update', $jobGroup->id) }}">
        @csrf
        @method('PUT')

        {{-- Basic Info --}}
        <div class="card" style="margin-bottom:18px">
            <div class="card-header" style="padding:18px 22px 14px">
                <span class="card-title"><i class="bi bi-info-circle" style="margin-right:8px;color:var(--primary)"></i>Group Details</span>
            </div>
            <div class="card-body">
                <div class="form-grid">

                    <div class="form-group">
                        <label class="form-label">Group Name <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $jobGroup->name) }}" required placeholder="Enter group name">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status <span style="color:var(--danger)">*</span></label>
                        <select name="status" class="form-control">
                            <option value="active" {{ old('status', $jobGroup->status) === 'active'    ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ old('status', $jobGroup->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="archived" {{ old('status', $jobGroup->status) === 'archived'  ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Optional description...">{{ old('description', $jobGroup->description) }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        {{-- Jobs --}}
        <div class="card" style="margin-bottom:18px">
            <div class="card-header" style="padding:18px 22px 14px">
                <span class="card-title"><i class="bi bi-briefcase" style="margin-right:8px;color:var(--primary)"></i>Assigned Jobs</span>
                <span style="font-size:12px;color:var(--text-muted)" id="selectedCount">
                    {{ count($selectedJobIds) }} selected
                </span>
            </div>
            <div class="card-body">

                {{-- Search filter --}}
                <input type="text" id="jobSearch" class="form-control" placeholder="Search jobs..." style="margin-bottom:12px" oninput="filterJobs()">

                <div id="jobsList" style="max-height:320px;overflow-y:auto;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:8px">
                    @forelse($jobs as $job)
                    <label id="job-row-{{ $job->id }}" style="display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:6px;cursor:pointer;transition:background .15s"
                        onmouseover="this.style.background='var(--body-bg)'"
                        onmouseout="this.style.background='transparent'">
                        <input type="checkbox"
                            name="job_ids[]"
                            value="{{ $job->id }}"
                            {{ in_array($job->id, $selectedJobIds) ? 'checked' : '' }}
                            onchange="updateCount()"
                            style="width:16px;height:16px;accent-color:var(--primary);flex-shrink:0">
                        <div style="flex:1;min-width:0">
                            <div style="font-size:13.5px;font-weight:600;color:var(--text-primary)">
                                {{ $job->job_no ?? '—' }}
                            </div>
                            <div style="font-size:12px;color:var(--text-muted)">
                                {{ $job->client_name ?? 'No client' }}
                                @if($job->job_id && $job->job_id !== '-')
                                · {{ $job->job_id }}
                                @endif
                            </div>
                        </div>
                        @if(in_array($job->id, $selectedJobIds))
                        <span style="font-size:11px;background:var(--primary-light);color:var(--primary);padding:2px 8px;border-radius:20px;font-weight:600">In Group</span>
                        @endif
                    </label>
                    @empty
                    <p style="text-align:center;color:var(--text-muted);padding:20px">No jobs found.</p>
                    @endforelse
                </div>

                <p style="font-size:12px;color:var(--text-muted);margin-top:8px">
                    <i class="bi bi-info-circle"></i> Check jobs to include in this group. Uncheck to remove.
                </p>
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;justify-content:space-between;align-items:center">
            <a href="{{ route('job-groups.index') }}" class="btn btn-outline">
                <i class="bi bi-x-lg"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Update Group
            </button>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
    function updateCount() {
        const checked = document.querySelectorAll('input[name="job_ids[]"]:checked').length;
        document.getElementById('selectedCount').textContent = checked + ' selected';
    }

    function filterJobs() {
        const query = document.getElementById('jobSearch').value.toLowerCase();
        document.querySelectorAll('#jobsList label').forEach(label => {
            const text = label.textContent.toLowerCase();
            label.style.display = text.includes(query) ? 'flex' : 'none';
        });
    }
</script>
@endpush