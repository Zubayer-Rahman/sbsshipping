@extends('layouts.app')

@section('title', 'Edit Job')
@section('page-title', 'Edit Job')
@section('breadcrumb', 'Jobs Manager / Edit / ' . $job->job_id)

@section('content')

<div style="max-width: 1200px; margin: 0 auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div>
            <h2 style="font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
                Edit Job — <span style="color:var(--primary)">{{ $job->job_id }}</span>
            </h2>
        </div>
        <div style="display:flex;gap:10px">
            <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline">
                <i class="bi bi-eye"></i> View
            </a>
            <a href="{{ route('jobs.list') }}" class="btn btn-outline">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('jobs.update', $job) }}">
        @csrf @method('PUT')

        {{-- ── SECTION 1: Client & Basic Info ── --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-person-circle" style="color:var(--primary);margin-right:8px"></i>Client & Reference</span>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Client Name</label>
                        <select name="client_name" class="form-select">
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->business_name }}"
                                {{ old('client_name', $job->client_name) == $client->business_name ? 'selected' : '' }}>
                                {{ $client->business_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Client Email</label>
                        <input type="email" name="client_email" class="form-control" value="{{ old('client_email', $job->client_email) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Client Phone</label>
                        <input type="text" name="client_phone" class="form-control" value="{{ old('client_phone', $job->client_phone) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Job Number (Manual)</label>
                        <input type="text" name="job_no" class="form-control" value="{{ old('job_no', $job->job_no) }}" placeholder="e.g. JB-1001">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SECTION 2: Shipment Details ── --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-box-seam" style="color:var(--accent);margin-right:8px"></i>Shipment Details</span>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">AWB / BL No.</label>
                        <input type="text" name="awb_no" class="form-control" value="{{ old('awb_no', $job->awb_no) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">— Select Category —</option>
                            @foreach(['Import by Air', 'Import by Sea', 'Export by Air', 'Export by Sea'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $job->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">— Select Type —</option>
                            <option value="FCL" {{ old('type', $job->type) == 'FCL' ? 'selected' : '' }}>FCL</option>
                            <option value="LCL" {{ old('type', $job->type) == 'LCL' ? 'selected' : '' }}>LCL</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quantity / Pkg</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $job->quantity) }}">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Items Description</label>
                        <input type="text" name="items" class="form-control" value="{{ old('items', $job->items) }}" placeholder="e.g. Electronics, Garments">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cargo Type</label>
                        <input type="text" name="cargo_type" class="form-control" value="{{ old('cargo_type', $job->cargo_type) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cargo Weight (KG)</label>
                        <input type="number" name="cargo_weight" class="form-control" step="0.01" value="{{ old('cargo_weight', $job->cargo_weight) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SECTION 3: Route & Carrier ── --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-truck" style="color:var(--success);margin-right:8px"></i>Route & Logistics</span>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Origin</label>
                        <input type="text" name="origin" class="form-control" value="{{ old('origin', $job->origin) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Destination</label>
                        <input type="text" name="destination" class="form-control" value="{{ old('destination', $job->destination) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vessel / Flight Name</label>
                        <input type="text" name="vessel_name" class="form-control" value="{{ old('vessel_name', $job->vessel_name) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Container No.</label>
                        <input type="text" name="container_no" class="form-control" value="{{ old('container_no', $job->container_no) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Shipping Agent</label>
                        <input type="text" name="shipping_agent" class="form-control" value="{{ old('shipping_agent', $job->shipping_agent) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Assigned User (Staff)</label>
                        <select name="assigned_user" class="form-select">
                            <option value="">Select Staff</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_user', $job->assigned_user) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SECTION 4: Dates & Timeline ── --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-calendar-event" style="color:#8b5cf6;margin-right:8px"></i>Dates & Timeline</span>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $job->start_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Receive Date</label>
                        <input type="date" name="receive_date" class="form-control" value="{{ old('receive_date', $job->receive_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Pickup Date</label>
                        <input type="date" name="pickup_date" class="form-control" value="{{ old('pickup_date', $job->pickup_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ETA Date</label>
                        <input type="date" name="eta_date" class="form-control" value="{{ old('eta_date', $job->eta_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Delivery Date</label>
                        <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', $job->delivery_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cleared On</label>
                        <input type="date" name="cleared_on" class="form-control" value="{{ old('cleared_on', $job->cleared_on?->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SECTION 5: Financials & Customs ── --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-file-earmark-ruled" style="color:var(--warning);margin-right:8px"></i>Customs & Financials</span>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Invoice No.</label>
                        <input type="text" name="invoice_no" class="form-control" value="{{ old('invoice_no', $job->invoice_no) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Invoice Date</label>
                        <input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', $job->invoice_date?->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ROT No.</label>
                        <input type="text" name="rot_no" class="form-control" value="{{ old('rot_no', $job->rot_no) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Buyer Name</label>
                        <input type="text" name="buyer_name" class="form-control" value="{{ old('buyer_name', $job->buyer_name) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Invoice Value (USD)</label>
                        <input type="number" name="invoice_value_usd" class="form-control" step="0.01" value="{{ old('invoice_value_usd', $job->invoice_value_usd) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Exchange Rate</label>
                        <input type="number" name="exchange_rate" class="form-control" step="0.01" value="{{ old('exchange_rate', $job->exchange_rate) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">IMP/EXP Value (৳)</label>
                        <input type="number" name="imp_exp_value" class="form-control" step="0.01" value="{{ old('imp_exp_value', $job->imp_exp_value) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">BE No.</label>
                        <input type="text" name="be_no" class="form-control" value="{{ old('be_no', $job->be_no) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">IP/EP No.</label>
                        <input type="text" name="ip_ep_no" class="form-control" value="{{ old('ip_ep_no', $job->ip_ep_no) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Billed Amount (৳)</label>
                        <input type="number" name="cost_amount" class="form-control" step="0.01" value="{{ old('cost_amount', $job->cost_amount) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Expense Amount (৳)</label>
                        <input type="number" name="expense_amount" class="form-control" step="0.01" value="{{ old('expense_amount', $job->expense_amount) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Job Status</label>
                        <select name="status" class="form-select">
                            @foreach(['Not Started', 'Pending','In-Transit','Delivered','Cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('status', $job->status) == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom:24px">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-card-text" style="color:var(--text-muted);margin-right:8px"></i>Staff Notes</span>
            </div>
            <div class="card-body">
                <textarea name="notes" class="form-control" style="min-height: 100px;">{{ old('notes', $job->notes) }}</textarea>
            </div>
        </div>

        <div style="display:flex;gap:12px;justify-content:flex-end;margin-bottom: 50px;">
            <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding: 10px 30px;">
                <i class="bi bi-check-lg"></i> Update Job Record
            </button>
        </div>
    </form>
</div>

@endsection

@push('styles')
<style>
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 8px;
        display: block;
    }

    .form-control,
    .form-select {
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        padding: 10px 12px;
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        width: 100%;
        transition: border-color 0.2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px var(--primary-glow);
    }
</style>
@endpush