@extends('layouts.app')
@section('title', 'Create Job Group')
@section('content')

<div style="padding:2rem;max-width:900px;margin:0 auto;font-family:'Inter',sans-serif">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <h1 style="font-size:1.75rem;font-weight:800;color:var(--text-primary);margin:0">
            Create Job Group
        </h1>
        <a href="{{ route('job-groups.index') }}"
            style="color:var(--primary);text-decoration:none;font-weight:600">← Back</a>
    </div>

    <div style="background:#fff;padding:32px;border-radius:12px;box-shadow:var(--shadow-md)">
        <form action="{{ route('job-groups.store') }}" method="POST">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">
                        Group Code
                    </label>
                    <input type="text" value="{{ $groupCode }}" disabled
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;
                                  background:var(--body-bg);font-size:14px;font-weight:600">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">
                        Status <span style="color:var(--danger)">*</span>
                    </label>
                    <select name="status" required
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:20px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">
                    Group Name <span style="color:var(--danger)">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    placeholder="e.g., Q1 2026 Electronics Imports, Container Batch #5"
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
            </div>

            <div style="margin-bottom:24px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">
                    Description
                </label>
                <textarea name="description" rows="3"
                    placeholder="Optional description for this group..."
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;
                                 font-size:14px;resize:vertical">{{ old('description') }}</textarea>
            </div>

            {{-- Job Selection --}}
            <div style="margin-bottom:24px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:10px">
                    Add Jobs to This Group
                </label>

                <input type="text" id="jobSearchInput"
                    placeholder="🔍 Search jobs by ID or client name..."
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);
                              border-radius:6px;font-size:14px;margin-bottom:10px"
                    oninput="filterJobsList(this.value)">

                <div style="border:1px solid var(--border);border-radius:8px;max-height:350px;overflow-y:auto;background:var(--body-bg)">
                    @forelse($jobs as $job)
                    <label class="job-item"
                        data-search="{{ strtolower(($job->job_id ?? '') . ' ' . ($job->client_name ?? '')) }}"
                        style="display:flex;align-items:center;gap:12px;padding:12px 14px;
                                  cursor:pointer;border-bottom:1px solid var(--border);background:#fff;
                                  transition:background .15s"
                        onmouseover="this.style.background='var(--primary-light)'"
                        onmouseout="this.style.background='#fff'">
                        <input type="checkbox" name="job_ids[]" value="{{ $job->id }}"
                            style="width:18px;height:18px;cursor:pointer">
                        <div style="flex:1">
                            <div style="font-weight:700;color:var(--primary);font-size:13px">
                                {{ $job->job_id ?? 'Job #'.$job->id }}
                            </div>
                            <div style="font-size:12px;color:var(--text-muted)">
                                👤 {{ $job->client_name ?? 'No client' }}
                                @if($job->category) • {{ $job->category }} @endif
                            </div>
                        </div>
                    </label>
                    @empty
                    <div style="padding:30px;text-align:center;color:var(--text-muted)">
                        No jobs available. Create some jobs first.
                    </div>
                    @endforelse
                </div>

                <p style="font-size:12px;color:var(--text-muted);margin:6px 0 0">
                    Select multiple jobs to add to this group
                </p>
            </div>

            <div style="display:flex;gap:12px">
                <button type="submit"
                    style="flex:1;padding:12px;background:var(--primary);color:#fff;border:none;
                               border-radius:6px;font-weight:600;cursor:pointer;font-size:14px">
                    Create Group
                </button>
                <a href="{{ route('job-groups.index') }}"
                    style="padding:12px 24px;background:#64748b;color:#fff;border-radius:6px;
                          text-decoration:none;font-weight:600;font-size:14px">Cancel</a>
            </div>
        </form>
    </div>

</div>

<script>
    function filterJobsList(query) {
        const q = query.toLowerCase().trim();
        document.querySelectorAll('.job-item').forEach(item => {
            const matches = !q || item.dataset.search.includes(q);
            item.style.display = matches ? 'flex' : 'none';
        });
    }
</script>

@endsection