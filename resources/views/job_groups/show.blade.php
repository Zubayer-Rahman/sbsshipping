@extends('layouts.app')
@section('title', $jobGroup->name)
@section('page-title', $jobGroup->name)
@section('breadcrumb', 'Job Groups / ' . $jobGroup->name)
@section('content')

@php
$totalBilled = 0;
$totalExpenses = 0;

foreach ($jobGroup->jobs as $job) {
$totalBilled += $job->imp_exp_value ?? 0;

$normalExpenses = $job->expenses()->sum('total_amount');
$additionalExpenses = \App\Models\AdditionalExpense::where('job_id', $job->id)->sum('actual_amount');
$iouExpenses = $job->ious()->sum('amount');

$totalExpenses += ($normalExpenses + $additionalExpenses + $iouExpenses);
}

$totalProfit = $totalBilled - $totalExpenses;
@endphp

<div style="max-width:1400px;margin:0 auto">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h2 style="font-size:20px;font-weight:800;color:var(--text-primary);margin-bottom:4px">
                {{ $jobGroup->name }}
            </h2>
            <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:13px;color:var(--text-muted)">
                <span><strong>Code:</strong> {{ $jobGroup->group_code }}</span>
                <span>
                    <strong>Status:</strong>
                    <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;
                        background:{{ $jobGroup->status === 'active' ? '#d1fae5' : ($jobGroup->status === 'completed' ? '#dbeafe' : '#f1f5f9') }};
                        color:{{ $jobGroup->status === 'active' ? '#065f46' : ($jobGroup->status === 'completed' ? '#1e40af' : '#64748b') }}">
                        {{ ucfirst($jobGroup->status) }}
                    </span>
                </span>
                <span><strong>Jobs:</strong> {{ $jobGroup->jobs->count() }}</span>
                @if($jobGroup->description)
                <span><strong>Description:</strong> {{ $jobGroup->description }}</span>
                @endif
                @if($jobGroup->creator)
                <span><strong>Created by:</strong> {{ $jobGroup->creator->name }}</span>
                @endif
            </div>
        </div>
        <div style="display:flex;gap:10px">
            <a href="{{ route('job-groups.index') }}" class="btn btn-outline btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <a href="{{ route('job-groups.edit', $jobGroup) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil"></i> Edit Group
            </a>
        </div>
    </div>

    {{-- Financial Summary Cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:24px">

        <div class="card" style="border-left:5px solid var(--primary);padding:20px">
            <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.06em">
                Total Billed
            </div>
            <div style="font-size:28px;font-weight:800;color:var(--text-primary);margin-top:8px">
                ৳ {{ number_format($totalBilled, 2) }}
            </div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">Gross revenue for this group</div>
        </div>

        <div class="card" style="border-left:5px solid var(--danger);padding:20px">
            <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.06em">
                Total Expenses
            </div>
            <div style="font-size:28px;font-weight:800;color:var(--danger);margin-top:8px">
                ৳ {{ number_format($totalExpenses, 2) }}
            </div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">Operational costs incurred</div>
        </div>

        <div class="card" style="border-left:5px solid {{ $totalProfit >= 0 ? 'var(--success)' : 'var(--danger)' }};padding:20px;background:{{ $totalProfit >= 0 ? '#f0fdf4' : '#fef2f2' }}">
            <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.06em">
                Net Profit / Loss
            </div>
            <div style="font-size:28px;font-weight:800;color:{{ $totalProfit >= 0 ? 'var(--success)' : 'var(--danger)' }};margin-top:8px">
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

    {{-- Jobs Breakdown Table --}}
    <div class="card">
        <div class="card-header" style="padding:18px 22px 14px">
            <span class="card-title">
                <i class="bi bi-briefcase" style="margin-right:8px;color:var(--primary)"></i>
                Jobs Breakdown
            </span>
            <span style="font-size:13px;color:var(--text-muted)">{{ $jobGroup->jobs->count() }} jobs in this group</span>
        </div>
        <div class="card-body" style="padding:0">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Job / Client</th>
                            <th>Category</th>
                            <th style="text-align:right">Billed (৳)</th>
                            <th style="text-align:right">Expenses (৳)</th>
                            <th style="text-align:right">Profit / Loss</th>
                            <th style="text-align:center">Status</th>
                            <th style="text-align:center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobGroup->jobs as $job)
                        @php
                        $jobNormal = $job->expenses()->sum('total_amount');
                        $jobAdditional = \App\Models\AdditionalExpense::where('job_id', $job->id)->sum('actual_amount');
                        $jobIou = $job->ious()->sum('amount');
                        $jobExpense = $jobNormal + $jobAdditional + $jobIou;
                        $jobBilled = $job->imp_exp_value ?? 0;
                        $jobProfit = $jobBilled - $jobExpense;
                        @endphp
                        <tr style="border-bottom:1px solid var(--border)">

                            {{-- Job / Client --}}
                            <td style="padding:14px 16px">
                                <a href="{{ route('jobs.show', $job->id) }}"
                                    style="font-weight:700;color:var(--primary);text-decoration:none;font-size:14px">
                                    {{ $job->job_no ?? $job->job_id ?? 'Job #' . $job->id }}
                                </a>
                                <div style="font-size:12px;color:var(--text-muted);margin-top:2px">
                                    {{ $job->client_name ?? '—' }}
                                </div>
                            </td>

                            {{-- Category --}}
                            <td style="padding:14px 16px;font-size:12px;color:var(--text-muted)">
                                {{ $job->category ?? '—' }}
                                @if($job->type)
                                <span style="display:inline-block;font-size:10px;font-weight:700;padding:1px 6px;border-radius:8px;background:#e0e7ff;color:#3730a3;margin-left:4px">
                                    {{ $job->type }}
                                </span>
                                @endif
                            </td>

                            {{-- Billed --}}
                            <td style="padding:14px 16px;text-align:right;font-weight:600">
                                {{ number_format($jobBilled, 2) }}
                            </td>

                            {{-- Expenses --}}
                            <td style="padding:14px 16px;text-align:right;color:var(--danger);font-weight:600">
                                {{ number_format($jobExpense, 2) }}
                                @if($jobNormal > 0 || $jobAdditional > 0 || $jobIou > 0)
                                <div style="font-size:10px;color:var(--text-muted);font-weight:400;margin-top:2px">
                                    @if($jobNormal > 0) Exp: {{ number_format($jobNormal, 0) }} @endif
                                    @if($jobAdditional > 0) · Add: {{ number_format($jobAdditional, 0) }} @endif
                                    @if($jobIou > 0) · IOU: {{ number_format($jobIou, 0) }} @endif
                                </div>
                                @endif
                            </td>

                            {{-- Profit/Loss --}}
                            <td style="padding:14px 16px;text-align:right;font-weight:800;color:{{ $jobProfit >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                                {{ $jobProfit < 0 ? '-' : '+' }}৳ {{ number_format(abs($jobProfit), 2) }}
                                @if($jobBilled > 0)
                                <div style="font-size:10px;font-weight:500;color:var(--text-muted);margin-top:2px">
                                    {{ number_format(($jobProfit / $jobBilled) * 100, 1) }}% margin
                                </div>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td style="padding:14px 16px;text-align:center">
                                @php
                                $statusColors = [
                                'completed' => ['#d1fae5','#065f46'],
                                'in-transit' => ['#dbeafe','#1e40af'],
                                'pending' => ['#fef3c7','#92400e'],
                                'cancelled' => ['#fee2e2','#991b1b'],
                                ];
                                $sc = $statusColors[strtolower($job->status ?? '')] ?? ['#f1f5f9','#64748b'];
                                @endphp
                                <span style="font-size:11px;padding:3px 10px;border-radius:20px;font-weight:700;background:{{ $sc[0] }};color:{{ $sc[1] }}">
                                    {{ ucfirst($job->status ?? 'pending') }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td style="padding:14px 16px;text-align:center">
                                <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-outline btn-sm">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:48px;color:var(--text-muted)">
                                <i class="bi bi-briefcase" style="font-size:40px;display:block;margin-bottom:10px;opacity:.3"></i>
                                No jobs in this group yet.
                                <a href="{{ route('job-groups.edit', $jobGroup) }}" style="color:var(--primary)">Add jobs →</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                    {{-- Grand Total Footer --}}
                    @if($jobGroup->jobs->count() > 0)
                    <tfoot>
                        <tr style="background:var(--body-bg);font-weight:800;border-top:2px solid var(--border)">
                            <td style="padding:16px;font-size:14px" colspan="2">GRAND TOTAL</td>
                            <td style="padding:16px;text-align:right;font-size:15px">
                                ৳ {{ number_format($totalBilled, 2) }}
                            </td>
                            <td style="padding:16px;text-align:right;font-size:15px;color:var(--danger)">
                                ৳ {{ number_format($totalExpenses, 2) }}
                            </td>
                            <td style="padding:16px;text-align:right;font-size:15px;color:{{ $totalProfit >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                                {{ $totalProfit < 0 ? '-' : '+' }}৳ {{ number_format(abs($totalProfit), 2) }}
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif

                </table>
            </div>
        </div>
    </div>

</div>
@endsection