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
        <form method="GET" action="{{ route('jobs.list') }}"
            style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">

            {{-- Select Client --}}
            <select name="client" class="form-select" style="flex:1;min-width:180px;max-width:260px">
                <option value="">Select Client</option>
                @foreach($clients as $client)
                <option value="{{ $client }}" {{ request('client') == $client ? 'selected' : '' }}>
                    {{ $client }}
                </option>
                @endforeach
            </select>

            {{-- Job Bill Number --}}
            <select name="job_bill" class="form-select" style="flex:1;min-width:180px;max-width:220px">
                <option value="">Job Bill Number</option>
                @foreach($jobNos as $jno)
                <option value="{{ $jno }}" {{ request('job_bill') == $jno ? 'selected' : '' }}>
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

            {{-- Date From --}}
            <input type="date" name="date_from" class="form-control"
                style="flex:1;min-width:150px;max-width:180px"
                value="{{ request('date_from') }}">

            {{-- Date To --}}
            <input type="date" name="date_to" class="form-control"
                style="flex:1;min-width:150px;max-width:180px"
                value="{{ request('date_to') }}">

            <button type="submit" class="btn btn-primary" style="min-width:90px">
                Filter
            </button>

            @if(request()->hasAny(['client','job_bill','status','date_from','date_to']))
            <a href="{{ route('jobs.list') }}" class="btn btn-outline">Clear</a>
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
                    <th>Type</th>
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
                $expense = floatval($job->expense_amount ?? 0);
                $billed = floatval($job->cost_amount ?? 0);
                $profitLoss = $billed - $expense;
                $sl = $jobs->total() - (($jobs->currentPage() - 1) * $jobs->perPage()) - $loop->index;
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
                    <td style="font-size:13px">{{ $job->type ?? '—' }}</td>
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
                            <a href="{{ route('jobs.show', $job) }}"
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

    {{-- Pagination --}}
    @if($jobs->hasPages())
    <div class="pagination-wrapper" style="display:flex;align-items:center;justify-content:space-between">
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
    }

    .pagination li a,
    .pagination li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid var(--border);
        color: var(--text-muted);
        text-decoration: none;
        transition: all .15s;
    }

    .pagination li a:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .pagination li.active span {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }
</style>
@endpush