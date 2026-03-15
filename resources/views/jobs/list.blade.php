@extends('layouts.app')

@section('title', 'Jobs List')
@section('page-title', 'Jobs List')
@section('breadcrumb', 'Jobs Manager / Jobs List')

@section('content')

{{-- ── Header ────────────────────────────────────────────────────────────── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div>
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            All Jobs
        </h2>
        <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
            {{ $jobs->total() }} total jobs found
        </p>
    </div>
    <a href="{{ route('jobs.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Create Job
    </a>
</div>

{{-- ── Filters ──────────────────────────────────────────────────────────────── --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 22px">
        <form method="GET" action="{{ route('jobs.list') }}"
            style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
            <div class="form-group" style="flex:1;min-width:200px;margin:0">
                <label class="form-label" style="font-size:12px">Search</label>
                <div style="position:relative">
                    <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px"></i>
                    <input type="text" name="search" class="form-control"
                        placeholder="Job ID, client, origin..."
                        style="padding-left:36px"
                        value="{{ request('search') }}">
                </div>
            </div>
            <div class="form-group" style="min-width:160px;margin:0">
                <label class="form-label" style="font-size:12px">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending'    ? 'selected' : '' }}>Pending</option>
                    <option value="in-transit" {{ request('status') == 'in-transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="delivered" {{ request('status') == 'delivered'  ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') == 'cancelled'  ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="height:42px">
                <i class="bi bi-funnel"></i> Filter
            </button>
            @if(request('search') || request('status'))
            <a href="{{ route('jobs.list') }}" class="btn btn-outline" style="height:42px">
                <i class="bi bi-x"></i> Clear
            </a>
            @endif
        </form>
    </div>
</div>

{{-- ── Table ────────────────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Client</th>
                    <th>Route</th>
                    <th>Cargo</th>
                    <th>Invoice</th>
                    <th>Agent</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date</th>
                    <th style="text-align:center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                <tr>
                    <td>
                        <span style="font-weight:700;color:var(--primary);font-size:13px">
                            {{ $job->job_id }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13.5px">{{ $job->client_name ?? '—' }}</div>
                        @if($job->client_phone)
                        <div style="font-size:11px;color:var(--text-muted)">{{ $job->client_phone }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px">
                        {{ $job->origin ?? '—' }}
                        @if($job->destination)
                        <span style="color:var(--text-muted)"> → </span>
                        {{ $job->destination }}
                        @endif
                    </td>
                    <td style="font-size:13px">
                        {{ $job->cargo_type ?? '—' }}
                        @if($job->cargo_weight)
                        <div style="font-size:11px;color:var(--text-muted)">{{ $job->cargo_weight }} KG</div>
                        @endif
                    </td>
                    <td style="font-weight:600">
                        ৳ {{ number_format($job->cost_amount ?? 0, 0) }}
                    </td>
                    <td style="font-size:13px;color:var(--text-muted)">
                        {{ $job->assigned_agent ?? '—' }}
                    </td>
                    <td>
                        <span class="badge badge-{{ str_replace(' ', '-', $job->status) }}">
                            {{ ucfirst($job->status) }}
                        </span>
                    </td>
                    <td>
                        @if($job->is_paid)
                        <span class="badge" style="background:#d1fae5;color:#065f46">Paid</span>
                        @else
                        <span class="badge" style="background:#fee2e2;color:#991b1b">Unpaid</span>
                        @endif
                    </td>
                    <td style="color:var(--text-muted);font-size:12px">
                        {{ $job->created_at->format('d M Y') }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;justify-content:center">
                            <a href="{{ route('jobs.show', $job) }}"
                                class="btn btn-sm btn-outline" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('jobs.edit', $job) }}"
                                class="btn btn-sm" title="Edit"
                                style="background:var(--primary-light);color:var(--primary);border:none">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('jobs.destroy', $job) }}"
                                onsubmit="return confirm('Delete this job?')" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:40px;color:var(--text-muted)">
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
    <div class="pagination-wrapper">
        {{ $jobs->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
    /* Override default Laravel pagination */
    nav[role="navigation"] {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .pagination {
        display: flex;
        gap: 4px;
        list-style: none;
    }

    .pagination li a,
    .pagination li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: var(--radius-sm);
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