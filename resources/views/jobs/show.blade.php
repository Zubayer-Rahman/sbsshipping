@extends('layouts.app')

@section('title', 'Job Details')
@section('page-title', 'Job Details')
@section('breadcrumb', 'Jobs Manager / ' . $job->job_id)

@section('content')

<div style="max-width: 1200px; margin: 0 auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div>
            <h2 style="font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
                {{ $job->job_id }}
            </h2>
            <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
                Created {{ $job->created_at->format('d M Y, h:i A') }}
                @if($job->job_no) · Job No: <strong>{{ $job->job_no }}</strong>@endif
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
        <span class="badge badge-{{ str_replace(' ', '-', strtolower($job->status ?? '')) }}"
            style="font-size:13px;padding:6px 14px">
            {{ ucfirst($job->status ?? 'N/A') }}
        </span>
        @if($job->is_paid)
        <span class="badge" style="background:#d1fae5;color:#065f46;font-size:13px;padding:6px 14px">✓ Paid</span>
        @else
        <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:13px;padding:6px 14px">✗ Unpaid</span>
        @endif
        @if($job->category)
        <span class="badge" style="background:#dbeafe;color:#1e40af;font-size:13px;padding:6px 14px">{{ $job->category }}</span>
        @endif
        @if($job->type)
        <span class="badge" style="background:#fef3c7;color:#92400e;font-size:13px;padding:6px 14px">Type: {{ $job->type }}</span>
        @endif
    </div>

    <div class="info-grid">

        {{-- 1. Client Information --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-person-circle" style="color:var(--primary);margin-right:6px"></i>Client Information</span>
            </div>
            <div class="card-body info-body">
                <x-info-row label="Client Name" value="{{ $job->client_name }}" />
                <x-info-row label="Email" value="{{ $job->client_email }}" />
                <x-info-row label="Phone" value="{{ $job->client_phone }}" />
                <x-info-row label="Buyer" value="{{ $job->buyer_name }}" />
                <x-info-row label="Assigned Agent" value="{{ $job->assigned_agent }}" />
            </div>
        </div>

        {{-- 2. Shipment & Cargo --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-box-seam" style="color:var(--accent);margin-right:6px"></i>Shipment & Cargo</span>
            </div>
            <div class="card-body info-body">
                <x-info-row label="AWB/BL No." value="{{ $job->awb_no }}" />
                <x-info-row label="Container No." value="{{ $job->container_no }}" />
                <x-info-row label="Items" value="{{ $job->items }}" />
                <x-info-row label="Quantity" value="{{ $job->quantity }}" />
                <x-info-row label="Cargo Type" value="{{ $job->cargo_type }}" />
                <x-info-row label="Weight" value="{{ $job->cargo_weight ? $job->cargo_weight . ' KG' : '—' }}" />
                <x-info-row label="Cargo Size" value="{{ $job->cargo_size }}" />
            </div>
        </div>

        {{-- 3. Route & Logistics --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-truck" style="color:var(--success);margin-right:6px"></i>Route & Logistics</span>
            </div>
            <div class="card-body info-body">
                <x-info-row label="Origin" value="{{ $job->origin }}" />
                <x-info-row label="Destination" value="{{ $job->destination }}" />
                <x-info-row label="Vessel/Flight" value="{{ $job->vessel_name }}" />
                <x-info-row label="Shipping Agent" value="{{ $job->shipping_agent }}" />
                <x-info-row label="Assigned Staff" value="{{ optional($job->user)->name }}" />
            </div>
        </div>

        {{-- 4. Dates Timeline --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-calendar-event" style="color:#8b5cf6;margin-right:6px"></i>Dates Timeline</span>
            </div>
            <div class="card-body info-body">
                <x-info-row label="Start Date" value="{{ optional($job->start_date)->format('d M Y') }}" />
                <x-info-row label="Receive Date" value="{{ optional($job->receive_date)->format('d M Y') }}" />
                <x-info-row label="Pickup Date" value="{{ optional($job->pickup_date)->format('d M Y') }}" />
                <x-info-row label="ETA Date" value="{{ optional($job->eta_date)->format('d M Y') }}" />
                <x-info-row label="Delivery Date" value="{{ optional($job->delivery_date)->format('d M Y') }}" />
                <x-info-row label="Cleared On" value="{{ optional($job->cleared_on)->format('d M Y') }}" />
            </div>
        </div>

        {{-- 5. Customs Documentation --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-file-earmark-ruled" style="color:#f59e0b;margin-right:6px"></i>Customs & Docs</span>
            </div>
            <div class="card-body info-body">
                <x-info-row label="Invoice No." value="{{ $job->invoice_no }}" />
                <x-info-row label="Invoice Date" value="{{ optional($job->invoice_date)->format('d M Y') }}" />
                <x-info-row label="ROT No." value="{{ $job->rot_no }}" />
                <x-info-row label="BE No." value="{{ $job->be_no }}" />
                <x-info-row label="BE Date" value="{{ optional($job->be_date)->format('d M Y') }}" />
                <x-info-row label="IP/EP No." value="{{ $job->ip_ep_no }}" />
                <x-info-row label="IP/EP Date" value="{{ optional($job->ip_ep_date)->format('d M Y') }}" />
            </div>
        </div>

        {{-- 6. Financial Summary --}}
        <div class="card financial-card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-cash-stack" style="color:var(--warning);margin-right:6px"></i>Financial Summary</span>
            </div>
            <div class="card-body info-body">
                <x-info-row label="Invoice Value (USD)" value="${{ number_format($job->invoice_value_usd ?? 0, 2) }}" />
                <x-info-row label="Exchange Rate" value="{{ $job->exchange_rate ? '৳ ' . number_format($job->exchange_rate, 2) : '—' }}" />
                <x-info-row label="IMP/EXP Value" value="৳ {{ number_format($job->imp_exp_value ?? 0, 2) }}" />
                <div style="border-top: 1px dashed var(--border); padding-top: 12px;"></div>
                <div class="info-row">
                    <span class="info-label">Billed Amount</span>
                    <span style="font-size:15px;font-weight:700;color:var(--primary)">৳ {{ number_format($job->cost_amount ?? 0, 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Expenses</span>
                    <span style="font-size:15px;font-weight:700;color:var(--danger)">৳ {{ number_format($job->expense_amount ?? 0, 2) }}</span>
                </div>
                <div class="info-row" style="background: var(--primary-light); padding: 10px; border-radius: 6px; margin-top: 6px;">
                    <span class="info-label" style="font-weight: 800; color: var(--primary)">Net Profit / Loss</span>
                    <span style="font-size:17px;font-weight:900;color:{{ $job->dues >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                        ৳ {{ number_format($job->dues ?? 0, 2) }}
                    </span>
                </div>
            </div>
        </div>

    </div>

    {{-- Notes --}}
    @if($job->notes)
    <div class="card" style="margin-top:18px">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-card-text" style="color:var(--text-muted);margin-right:6px"></i>Staff Notes</span>
        </div>
        <div class="card-body">
            <p style="font-size:14px;color:var(--text-primary);line-height:1.7;white-space:pre-wrap;margin:0">{{ $job->notes }}</p>
        </div>
    </div>
    @endif

</div>

@endsection

@push('styles')
<style>
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(auto, 1fr));
        gap: 18px;
        align-items: start;
    }

    .info-body {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 18px 22px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        padding: 4px 0;
    }

    .info-label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .info-value {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        text-align: right;
        max-width: 60%;
        word-break: break-word;
    }

    .info-value.empty {
        color: var(--text-muted);
        font-weight: 400;
        font-style: italic;
    }

    .financial-card {
        background: linear-gradient(180deg, #fff 70%, var(--primary-light) 100%);
    }
</style>
@endpush