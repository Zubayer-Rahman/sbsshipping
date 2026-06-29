@extends('layouts.app')

@section('title', 'Job Lists')
@section('page-title', 'Job Lists')
@section('breadcrumb', 'Jobs Manager / Job List')

@section('content')

{{-- ── Header ── --}}
<div style="margin-bottom:20px">
    <h2 style="font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary);text-transform:uppercase;letter-spacing:.04em">
        JOB LISTS
    </h2>
</div>

{{-- ── Filter Box ── --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:18px 22px">
        <div style="font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:14px">Filter Jobs</div>
        <form method="GET" action="{{ route('jobs.list') }}" id="filterForm"
            style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">

            {{-- Select Client --}}
            <select name="client" id="clientFilter" class="form-select"
                style="flex:1;min-width:180px;max-width:260px"
                onchange="filterJobsByClient(this.value)">
                <option value="">Select Client</option>
                @foreach($clients as $client)
                <option value="{{ $client }}" {{ request('client') == $client ? 'selected' : '' }}>
                    {{ $client }}
                </option>
                @endforeach
            </select>

            {{-- Job Bill Number (filtered by client) --}}
            <select name="job_bill" id="jobBillFilter" class="form-select"
                style="flex:1;min-width:180px;max-width:220px">
                <option value="">Job Bill Number</option>
                @foreach($jobNos as $jno)
                <option value="{{ $jno }}"
                    {{ request('job_bill') == $jno ? 'selected' : '' }}>
                    {{ $jno }}
                </option>
                @endforeach
            </select>

            {{-- Select Status --}}
            <select name="status" class="form-select" style="flex:1;min-width:160px;max-width:200px">
                <option value="">Select Status</option>
                <option value="Not Started" {{ request('status')=='Not Started' ?'selected':'' }}>Not Started</option>
                <option value="In Progress" {{ request('status')=='In Progress'  ?'selected':'' }}>In Progress</option>
                <option value="pending" {{ request('status')=='pending'      ?'selected':'' }}>Pending</option>
                <option value="in-transit" {{ request('status')=='in-transit'   ?'selected':'' }}>In Transit</option>
                <option value="delivered" {{ request('status')=='delivered'    ?'selected':'' }}>Delivered</option>
                <option value="cancelled" {{ request('status')=='cancelled'    ?'selected':'' }}>Cancelled</option>
            </select>

            {{-- Single Date --}}
            <input type="date" name="date_from" class="form-control"
                style="flex:1;min-width:150px;max-width:180px"
                value="{{ request('date_from') }}">

            {{-- Today only checkbox --}}
            <label style="display:flex;align-items:center;gap:7px;font-size:13px;
                          font-weight:600;color:var(--text-primary);cursor:pointer;
                          white-space:nowrap;padding:8px 12px;border:1.5px solid var(--border);
                          border-radius:var(--radius-sm);background:#fff;
                          {{ request('today') ? 'border-color:var(--primary);background:var(--primary-light);color:var(--primary)' : '' }}">
                <input type="checkbox" name="today" value="1"
                    {{ request('today') ? 'checked' : '' }}
                    onchange="this.form.submit()"
                    style="accent-color:var(--primary);width:15px;height:15px">
                Today only
            </label>

            <button type="submit" class="btn btn-primary" style="min-width:90px">
                <i class="bi bi-funnel"></i> Filter
            </button>

            {{-- Live Search --}}
            <input type="text" id="liveSearch" placeholder="Search..."
                class="form-control"
                style="width:auto;padding:6px 20px;font-size:13px;min-width:200px"
                value="{{ request('search') }}">

            @if(request()->hasAny(['client','job_bill','status','date_from','today']))
            <a href="{{ route('jobs.list') }}" class="btn btn-outline">
                <i class="bi bi-x-lg"></i> Clear
            </a>
            @endif

        </form>
    </div>
</div>

{{-- ── Jobs Table ── --}}
<div class="card">
    <div class="card-body" style="padding:18px 22px 10px">
        <div style="font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:14px">Job List</div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">SL.</th>
                    <th>Job No</th>
                    <th>B/E Number</th>
                    <th>Received Date</th>
                    <th style="text-align:center">Client Name</th>
                    <th>AWB NO</th>
                    <th>Quantity</th>
                    <th>Category</th>
                    <th style="text-align:right">Total Expenses</th>
                    <th style="text-align:right">Billed Amount</th>
                    <th style="text-align:right">Profit / Loss</th>
                    <th>Status</th>
                    <th style="text-align:center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                @php
                // Normal Expenses (through pivot)
                $normalExpenses = $job->expenses()->sum('total_amount');

                // Additional Expenses (direct only - no pivot)
                $additionalExpenses = \App\Models\AdditionalExpense::where('job_id', $job->id)
                ->sum('to_be_billed');

                // IOUs (through pivot)
                $iouExpenses = $job->ious()->sum('amount');

                $expense = $normalExpenses + $additionalExpenses + $iouExpenses;
                $billed = $job->imp_exp_value ?? 0;
                $profitLoss = $billed - $expense;

                $sl = $jobs->total() - (($jobs->currentPage() - 1) * $jobs->perPage()) - $loop->index;
                $category = $job->category ?? '';
                @endphp
                <tr>
                    <td style="color:var(--text-muted);font-size:13px">{{ $sl }}</td>
                    <td>
                        <a href="{{ route('jobs.show', $job) }}"
                            style="font-weight:700;color:var(--primary);font-size:13px;text-decoration:none">
                            {{ $job->job_no ?? $job->job_id ?? '—' }}
                        </a>
                    </td>
                    <td style="font-size:13px">{{ $job->be_no ?? '—' }}</td>
                    <td style="font-size:13px;color:var(--text-muted)">
                        {{ $job->receive_date ? $job->receive_date->format('Y-m-d') : '—' }}
                    </td>
                    <td style="font-size:13px;text-align:center;font-weight:500">
                        {{ $job->client_name ?? '—' }}
                    </td>
                    <td style="font-size:13px">{{ $job->awb_no ?? '—' }}</td>
                    <td style="font-size:13px">{{ $job->quantity ?? '—' }}</td>
                    <td style="font-size:13px">{{ $category }}</td>
                    <td style="text-align:right;font-size:13px;font-weight:500">
                        {{ number_format($expense, 2) }}
                    </td>
                    <td style="text-align:right;font-size:13px;font-weight:500">
                        {{ number_format($billed, 2) }}
                    </td>
                    <td style="text-align:right;font-size:13px;font-weight:700; color:{{ $profitLoss >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                        {{ number_format($profitLoss, 2) }}
                    </td>
                    <td>
                        @php
                        $statusColors = [
                        'Not Started' => 'background:#e2e8f0;color:#475569',
                        'In Progress' => 'background:#dbeafe;color:#1e40af',
                        'pending' => 'background:#fef3c7;color:#92400e',
                        'in-transit' => 'background:#dbeafe;color:#1e40af',
                        'delivered' => 'background:#d1fae5;color:#065f46',
                        'cancelled' => 'background:#fee2e2;color:#991b1b',
                        ];
                        $sc = $statusColors[$job->status] ?? 'background:#e2e8f0;color:#475569';
                        @endphp
                        <span style="display:inline-block;padding:3px 10px;border-radius:20px; font-size:11px;font-weight:700;white-space:nowrap;{{ $sc }}">
                            {{ $job->status ?? 'Not Started' }}
                        </span>
                    </td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center">
                            <a href="{{ route('jobs.edit', $job) }}"
                                style="display:inline-flex;align-items:center;gap:4px; padding:5px 12px;border-radius:5px; background:#1a56db;color:#fff; font-size:12px;font-weight:600;text-decoration:none; transition:background .15s"
                                onmouseover="this.style.background='#1340b0'"
                                onmouseout="this.style.background='#1a56db'">
                                Edit
                            </a>
                            <a href="{{ route('jobs.print', $job) }}" target="_blank" class="btn btn-outline"
                                style="display:inline-flex; align-items:center; gap:4px; padding:5px 12px; border-radius:5px; background:#10b981; color:#fff; font-size:12px; font-weight:600; text-decoration:none; transition:background .15s"
                                onmouseover="this.style.background='#059669'"
                                onmouseout="this.style.background='#10b981'">
                                View PDF
                            </a>

                            <a>
                                <form method="POST" action="{{ route('jobs.destroy', $job) }}" onsubmit="return confirm('Are you sure you want to delete this job?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        style="display:inline-flex;align-items:center;gap:4px; padding:5px 12px;border-radius:5px; background:#ef4444;color:#fff; font-size:12px;font-weight:600;text-decoration:none; transition:background .15s; border:none; cursor:pointer"
                                        onmouseover="this.style.background='#dc2626'"
                                        onmouseout="this.style.background='#ef4444'">
                                        Delete
                                    </button>
                                </form>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="text-align:center;padding:48px;color:var(--text-muted)">
                        <i class="bi bi-inbox" style="font-size:40px;display:block;margin-bottom:10px;opacity:.35"></i>
                        No jobs found.
                        <a href="{{ route('jobs.create') }}" style="color:var(--primary)">Create one now →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($jobs->hasPages())
    <div class="pagination-wrapper">
        <div style="font-size:13px;color:var(--text-muted)">
            Showing {{ $jobs->firstItem() }}–{{ $jobs->lastItem() }} of {{ $jobs->total() }} jobs
        </div>
        {{ $jobs->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
    .pagination-wrapper {
        padding: 16px 22px;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    nav[role="navigation"] {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .pagination {
        display: flex;
        gap: 4px;
        list-style: none;
        margin: 0;
        padding: 0;
        flex-wrap: wrap;
    }

    .pagination li a,
    .pagination li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 8px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: 1.5px solid var(--border);
        color: var(--text-muted);
        text-decoration: none;
        transition: all .15s;
        background: #fff;
        white-space: nowrap;
    }

    .pagination li a:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
    }

    .pagination li.active span {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
        box-shadow: 0 2px 8px rgba(26, 86, 219, .25);
    }

    /* Disabled prev/next */
    .pagination li span[aria-disabled="true"],
    .pagination li span.disabled {
        opacity: .4;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Hide the "..." dots styling weirdness */
    .pagination li span[aria-label="..."] {
        border-color: transparent;
        background: transparent;
        pointer-events: none;
    }
</style>
@endpush


@push('scripts')
<script>
    const jobsByClient = @json(
    \App\Models\Job::whereNotNull('job_no')
        ->get(['id', 'job_no', 'client_name'])
        ->groupBy('client_name')
        ->map(fn($jobs) => $jobs->pluck('job_no'))
);

    function filterJobsByClient(client) {
        const jobSelect = document.getElementById('jobBillFilter');
        const current = "{{ request('job_bill') }}";

        // Reset
        jobSelect.innerHTML = '<option value="">Job Bill Number</option>';

        if (!client || !jobsByClient[client]) return;

        jobsByClient[client].forEach(jobNo => {
            const opt = document.createElement('option');
            opt.value = jobNo;
            opt.textContent = jobNo;
            if (jobNo === current) opt.selected = true;
            jobSelect.appendChild(opt);
        });
    }

    // Run on page load to restore job list if client is pre-selected
    document.addEventListener('DOMContentLoaded', () => {
        const client = document.getElementById('clientFilter').value;
        if (client) filterJobsByClient(client);
    });

    // Live Search
    document.getElementById('liveSearch').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(searchTerm) ? '' : 'none';
        });
    });
</script>
@endpush