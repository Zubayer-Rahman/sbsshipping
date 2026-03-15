@extends('layouts.app')

@section('title', 'Job Details')
@section('page-title', 'Job Details')
@section('breadcrumb', 'Jobs Manager / ' . $job->job_id)

@section('content')

<div style="max-width:900px">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div>
            <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
                {{ $job->job_id }}
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Created {{ $job->created_at->format('d M Y, h:i A') }}
            </p>
        </div>
        <div style="display:flex;gap:10px">
            <a href="{{ route('jobs.edit', $job) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit Job
            </a>
            <a href="{{ route('jobs.list') }}" class="btn btn-outline">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Status Banner --}}
    <div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap">
        <span class="badge badge-{{ str_replace(' ', '-', $job->status) }}"
              style="font-size:13px;padding:6px 14px">
            {{ ucfirst($job->status) }}
        </span>
        @if($job->is_paid)
            <span class="badge" style="background:#d1fae5;color:#065f46;font-size:13px;padding:6px 14px">
                ✓ Paid
            </span>
        @else
            <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:13px;padding:6px 14px">
                ✗ Unpaid
            </span>
        @endif
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px">

        {{-- Client Info --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-person-circle" style="color:var(--primary);margin-right:6px"></i>Client</span>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:12px">
                @foreach(['client_name'=>'Name','client_email'=>'Email','client_phone'=>'Phone','assigned_agent'=>'Agent'] as $field => $label)
                <div style="display:flex;gap:8px">
                    <span style="font-size:12px;color:var(--text-muted);width:80px;flex-shrink:0;padding-top:1px">{{ $label }}</span>
                    <span style="font-size:14px;font-weight:500">{{ $job->$field ?? '—' }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Route & Cargo --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-geo-alt" style="color:var(--accent);margin-right:6px"></i>Route & Cargo</span>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:12px">
                @foreach(['origin'=>'Origin','destination'=>'Destination','cargo_type'=>'Type','cargo_weight'=>'Weight','cargo_size'=>'Size'] as $field => $label)
                <div style="display:flex;gap:8px">
                    <span style="font-size:12px;color:var(--text-muted);width:90px;flex-shrink:0;padding-top:1px">{{ $label }}</span>
                    <span style="font-size:14px;font-weight:500">
                        {{ $job->$field ? ($field == 'cargo_weight' ? $job->$field . ' KG' : $job->$field) : '—' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Dates --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-calendar3" style="color:var(--success);margin-right:6px"></i>Dates</span>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:12px">
                @foreach(['pickup_date'=>'Pickup','eta_date'=>'ETA','delivery_date'=>'Delivered'] as $field => $label)
                <div style="display:flex;gap:8px">
                    <span style="font-size:12px;color:var(--text-muted);width:90px;flex-shrink:0;padding-top:1px">{{ $label }}</span>
                    <span style="font-size:14px;font-weight:500">
                        {{ $job->$field ? $job->$field->format('d M Y') : '—' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Financial --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-cash" style="color:var(--warning);margin-right:6px"></i>Financials</span>
            </div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:12px">
                <div style="display:flex;gap:8px">
                    <span style="font-size:12px;color:var(--text-muted);width:90px;flex-shrink:0;padding-top:1px">Invoice</span>
                    <span style="font-size:14px;font-weight:700;color:var(--primary)">৳ {{ number_format($job->cost_amount ?? 0, 2) }}</span>
                </div>
                <div style="display:flex;gap:8px">
                    <span style="font-size:12px;color:var(--text-muted);width:90px;flex-shrink:0;padding-top:1px">Expense</span>
                    <span style="font-size:14px;font-weight:600;color:var(--danger)">৳ {{ number_format($job->expense_amount ?? 0, 2) }}</span>
                </div>
                <div style="border-top:1px solid var(--border);padding-top:10px;display:flex;gap:8px">
                    <span style="font-size:12px;color:var(--text-muted);width:90px;flex-shrink:0;padding-top:1px">Due</span>
                    <span style="font-size:16px;font-weight:800;color:var(--success)">৳ {{ number_format($job->dues, 2) }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Notes --}}
    @if($job->notes)
    <div class="card" style="margin-top:18px">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-card-text" style="color:var(--text-muted);margin-right:6px"></i>Notes</span>
        </div>
        <div class="card-body">
            <p style="font-size:14px;color:var(--text-primary);line-height:1.7;white-space:pre-wrap">{{ $job->notes }}</p>
        </div>
    </div>
    @endif

</div>

@endsection