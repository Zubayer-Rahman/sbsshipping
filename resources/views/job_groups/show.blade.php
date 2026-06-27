@extends('layouts.app')
@section('title', $jobGroup->name)
@section('content')

@php
// ✅ Calculate totals from ALL jobs in this group
    $totalBilled = 0;
    $totalExpenses = 0;

    foreach ($jobGroup->jobs as $job) {
    // Billed amount
    $totalBilled = \App\Models\Job::where('job_id', $job->id)->sum('imp_exp_value');//$job->imp_exp_value ?? 0;

    // Calculate expenses for each job
    $normalExpenses = $job->expenses()->sum('total_amount');
    $additionalExpenses = \App\Models\AdditionalExpense::where('job_id', $job->id)->sum('actual_amount');
    $iouExpenses = $job->ious()->sum('amount');

    $totalExpenses += ($normalExpenses + $additionalExpenses + $iouExpenses);
    }

    $totalProfit = $totalBilled - $totalExpenses;
@endphp

<div style="padding:2rem;max-width:1400px;margin:0 auto;font-family:'Inter',sans-serif">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <a href="{{ route('job-groups.index') }}" style="color:var(--primary);text-decoration:none;font-weight:600">← Back to Groups</a>
        <div style="display:flex;gap:10px">
            <a href="{{ route('job-groups.edit', $jobGroup) }}" style="padding:8px 16px;background:var(--primary);color:#fff;border-radius:6px;text-decoration:none;font-size:13px;font-weight:600">Edit Group</a>
        </div>
    </div>

    {{-- Group Info Header --}}
    <div style="background:#fff;padding:24px;border-radius:12px;box-shadow:var(--shadow-sm);margin-bottom:24px">
        <h1 style="margin:0 0 8px 0;font-size:24px;font-weight:800;color:var(--text-primary)">
            {{ $jobGroup->name }}
        </h1>
        <div style="display:flex;gap:20px;flex-wrap:wrap;font-size:13px;color:var(--text-muted)">
            <span><strong>Code:</strong> {{ $jobGroup->group_code }}</span>
            <span><strong>Status:</strong> {{ ucfirst($jobGroup->status) }}</span>
            <span><strong>Jobs:</strong> {{ $jobGroup->jobs->count() }}</span>
            @if($jobGroup->description)
            <span><strong>Description:</strong> {{ $jobGroup->description }}</span>
            @endif
        </div>
    </div>

    {{-- Financial Summary Cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:24px">
        <div style="background:#fff;padding:24px;border-radius:12px;box-shadow:var(--shadow-sm);border-left:5px solid var(--primary)">
            <div style="font-size:12px;color:var(--text-muted);font-weight:700;text-transform:uppercase">Total Billed</div>
            <div style="font-size:28px;font-weight:800;color:var(--text-primary);margin-top:8px">৳ {{ number_format($totalBilled, 2) }}</div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">Gross revenue for this group</div>
        </div>

        <div style="background:#fff;padding:24px;border-radius:12px;box-shadow:var(--shadow-sm);border-left:5px solid #ef4444">
            <div style="font-size:12px;color:var(--text-muted);font-weight:700;text-transform:uppercase">Total Expenses</div>
            <div style="font-size:28px;font-weight:800;color:#ef4444;margin-top:8px">৳ {{ number_format($totalExpenses, 2) }}</div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">Operational costs incurred</div>
        </div>

        <div style="background:{{ $totalProfit >= 0 ? '#f0fdf4' : '#fef2f2' }};padding:24px;border-radius:12px;box-shadow:var(--shadow-sm);border-left:5px solid {{ $totalProfit >= 0 ? '#10b981' : '#ef4444' }}">
            <div style="font-size:12px;color:var(--text-muted);font-weight:700;text-transform:uppercase">Net Profit/Loss</div>
            <div style="font-size:28px;font-weight:800;color:{{ $totalProfit >= 0 ? '#10b981' : '#ef4444' }};margin-top:8px">
                {{ $totalProfit < 0 ? '-' : '' }}৳ {{ number_format(abs($totalProfit), 2) }}
            </div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">
                @if($totalProfit >= 0)
                Margin: {{ $totalBilled > 0 ? number_format(($totalProfit / $totalBilled) * 100, 1) : 0 }}%
                @else
                Loss on this group
                @endif
            </div>
        </div>
    </div>

    {{-- Individual Jobs Breakdown --}}
    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-sm);border:1px solid var(--border)">
        <div style="padding:18px 24px;background:var(--body-bg);border-bottom:1px solid var(--border)">
            <h2 style="margin:0;font-size:16px;font-weight:700">Jobs Breakdown</h2>
        </div>
        <table style="width:100%;border-collapse:collapse">
            <thead style="background:#fafbfc">
                <tr>
                    <th style="padding:12px 24px;text-align:left;font-size:11px;color:var(--text-muted);text-transform:uppercase">Job ID / Client</th>
                    <th style="padding:12px 24px;text-align:right;font-size:11px;color:var(--text-muted);text-transform:uppercase">Billed (৳)</th>
                    <th style="padding:12px 24px;text-align:right;font-size:11px;color:var(--text-muted);text-transform:uppercase">Expense (৳)</th>
                    <th style="padding:12px 24px;text-align:right;font-size:11px;color:var(--text-muted);text-transform:uppercase">Profit/Loss</th>
                    <th style="padding:12px 24px;text-align:center;font-size:11px;color:var(--text-muted);text-transform:uppercase">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobGroup->jobs as $job)
                @php
                // ✅ Calculate per-job expenses
                $jobNormal = $job->expenses()->sum('total_amount');
                $jobAdditional = \App\Models\AdditionalExpense::where('job_id', $job->id)->sum('to_be_billed');
                $jobIou = $job->ious()->sum('amount');

                $jobExpense = $jobNormal + $jobAdditional + $jobIou;
                $jobBilled = $job->imp_exp_value ?? 0;
                $jobProfit = $jobBilled - $jobExpense;
                @endphp
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:16px 24px">
                        <a href="{{ route('jobs.show', $job->id) }}" style="font-weight:700;color:var(--primary);text-decoration:none">
                            {{ $job->job_no ?? $job->job_id ?? 'Job #' . $job->id }}
                        </a>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:2px">{{ $job->client_name ?? '—' }}</div>
                    </td>
                    <td style="padding:16px 24px;text-align:right;font-weight:600">
                        {{ number_format($jobBilled, 2) }}
                    </td>
                    <td style="padding:16px 24px;text-align:right;color:#ef4444">
                        {{ number_format($jobExpense, 2) }}
                    </td>
                    <td style="padding:16px 24px;text-align:right;font-weight:700;color:{{ $jobProfit >= 0 ? '#10b981' : '#ef4444' }}">
                        {{ $jobProfit < 0 ? '-' : '' }}৳ {{ number_format(abs($jobProfit), 2) }}
                    </td>
                    <td style="padding:16px 24px;text-align:center">
                        <span style="font-size:11px;padding:3px 10px;border-radius:10px;background:var(--body-bg);color:var(--text-muted);font-weight:600">
                            {{ ucfirst($job->status ?? 'pending') }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:32px;text-align:center;color:var(--text-muted)">
                        No jobs in this group yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot style="background:var(--body-bg);font-weight:800">
                <tr>
                    <td style="padding:16px 24px">GRAND TOTAL</td>
                    <td style="padding:16px 24px;text-align:right">৳ {{ number_format($totalBilled, 2) }}</td>
                    <td style="padding:16px 24px;text-align:right;color:#ef4444">৳ {{ number_format($totalExpenses, 2) }}</td>
                    <td style="padding:16px 24px;text-align:right;color:{{ $totalProfit >= 0 ? '#10b981' : '#ef4444' }}">
                        {{ $totalProfit < 0 ? '-' : '' }}৳ {{ number_format(abs($totalProfit), 2) }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection