@extends('layouts.app')

@section('content')
<style>
    .release-list-container {
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        font-family: 'Inter', sans-serif;
    }

    .back-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: var(--radius-sm);
        margin-bottom: 1.5rem;
        font-family: 'Inter', sans-serif;
    }

    .alert-success {
        background: #d1fae5;
        border-left: 4px solid var(--success);
        color: #065f46;
    }

    .filter-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        margin-bottom: 1.5rem;
    }

    .filter-row {
        display: flex;
        gap: 1rem;
        align-items: end;
    }

    .form-group {
        flex: 1;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
        font-weight: 500;
        font-size: 0.875rem;
        font-family: 'Inter', sans-serif;
    }

    .form-control {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: 'Inter', sans-serif;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .btn {
        padding: 0.625rem 1.5rem;
        border-radius: var(--radius-sm);
        font-weight: 500;
        font-family: 'Inter', sans-serif;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        box-shadow: 0 4px 12px var(--primary-glow);
    }

    .btn-secondary {
        background: var(--text-muted);
        color: white;
    }

    .btn-secondary:hover {
        background: #475569;
    }

    .table-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Inter', sans-serif;
    }

    .data-table thead {
        background: var(--body-bg);
    }

    .data-table th {
        padding: 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
    }

    .data-table td {
        padding: 1rem;
        border-top: 1px solid var(--border);
    }

    .data-table tbody tr {
        transition: background 0.2s;
    }

    .data-table tbody tr:hover {
        background: var(--primary-light);
    }

    .text-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .text-link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
        color: var(--text-muted);
    }

    .pagination {
        margin-top: 1.5rem;
    }
</style>

<div class="release-list-container">
    <div class="page-header">
        <h1 class="page-title">IOU Release List</h1>
        <a href="{{ route('ious.index') }}" class="back-link">← Back to IOUs</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Search Filter -->
    <div class="filter-card">
        <form method="GET">
            <div class="filter-row">
                <div class="form-group">
                    <input type="text" name="search" placeholder="Search by IOU ref or contact..."
                        value="{{ request('search') }}" class="form-control">
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('ious.release-list') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Released IOUs Table -->
    <div class="table-card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>IOU Ref #</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Against</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right">Total Expensed</th> {{-- New Column --}}
                        <th class="text-right">Balance/Extra</th> {{-- New Column --}}
                        <th>Released Date</th>
                        <th>Released By</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($releasedIous as $iou)
                    {{-- 1. Calculate the values for this specific IOU --}}
                    @php
                    $totalExpensed = $iou->payments->sum('amount');
                    $diff = $iou->amount - $totalExpensed;
                    @endphp

                    <tr>
                        <td>
                            <a href="{{ route('ious.show', $iou) }}" class="text-link">
                                {{ $iou->reference_number }}
                            </a>
                        </td>
                        <td>
                            <span class="badge {{ $iou->type == 'receivable' ? 'badge-success' : 'badge-danger' }}">
                                {{ $iou->type == 'receivable' ? 'Received' : 'Paid' }}
                            </span>
                        </td>
                        <td>{{ $iou->contact->name }}</td>

                        {{-- Updated Against column to show multiple jobs --}}
                        <td>
                            @if($iou->jobs && $iou->jobs->count() > 0)
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                @foreach($iou->jobs as $job)
                                <span style="font-size: 11px; background: var(--primary-light); color: var(--primary); padding: 2px 6px; border-radius: 4px; font-weight: 600;">
                                    {{ $job->job_no ?? $job->job_id }}
                                </span>
                                @endforeach
                            </div>
                            @else
                            {{ $iou->against ?? '-' }}
                            @endif
                        </td>

                        <td class="text-right">৳{{ number_format($iou->amount, 2) }}</td>

                        {{-- Total Expensed --}}
                        <td class="text-right" style="font-weight: 700; color: var(--primary);">
                            ৳{{ number_format($totalExpensed, 2) }}
                        </td>

                        {{-- Balance or Extra Logic --}}
                        <td class="text-right">
                            @if($diff < 0)
                                <span style="color: var(--danger); font-weight: 700;">
                                Extra: ৳{{ number_format(abs($diff), 2) }}
                                </span>
                                @else
                                <span style="color: var(--text-muted); font-weight: 700;">
                                    Bal: ৳{{ number_format($diff, 2) }}
                                </span>
                                @endif
                        </td>

                        <td>{{ $iou->released_at ? $iou->released_at->format('d M Y') : '-' }}</td>
                        <td>{{ $iou->releasedBy->name ?? '-' }}</td>

                        <td class="text-center">
                            <a href="{{ route('ious.show', $iou) }}" class="text-link">View IOU</a>
                            {{-- Link to the general expense list filtered by this IOU if needed --}}
                            <a href="{{ route('ious.expense-list', ['search' => $iou->reference_number]) }}" class="text-link" style="margin-left: 0.5rem;">View Payments</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="empty-state">
                            No released IOUs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        {{ $releasedIous->links() }}
    </div>
</div>
@endsection