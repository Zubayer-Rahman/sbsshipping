@extends('layouts.app')

@section('title', 'Edit Job')
@section('page-title', 'Edit Job')
@section('breadcrumb', 'Jobs Manager / Edit / ' . $job->job_id)

@section('content')

<div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div>
            <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
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

        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-person-circle" style="color:var(--primary);margin-right:8px"></i>Client Information</span>
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
                        <!-- <label class="form-label">Client Name</label>
                        <input type="text" name="client_name" class="form-control" value="{{ old('client_name', $job->client_name) }}"> -->
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
                        <label class="form-label">Assigned Agent</label>
                        <input type="text" name="assigned_agent" class="form-control" value="{{ old('assigned_agent', $job->assigned_agent) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-geo-alt" style="color:var(--accent);margin-right:8px"></i>Route & Cargo</span>
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
                        <label class="form-label">Cargo Type</label>
                        <select name="cargo_type" class="form-select">
                            <option value="">— Select —</option>
                            @foreach(['General','Fragile','Hazardous','Perishable','Oversized','Electronics','Livestock'] as $type)
                            <option value="{{ $type }}" {{ old('cargo_type', $job->cargo_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cargo Weight (KG)</label>
                        <input type="number" name="cargo_weight" class="form-control" step="0.01" value="{{ old('cargo_weight', $job->cargo_weight) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cargo Size</label>
                        <input type="text" name="cargo_size" class="form-control" value="{{ old('cargo_size', $job->cargo_size) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['pending','in-transit','delivered','cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('status', $job->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-calendar3" style="color:var(--success);margin-right:8px"></i>Dates</span>
            </div>
            <div class="card-body">
                <div class="form-grid-3">
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
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-currency-dollar" style="color:var(--warning);margin-right:8px"></i>Financials</span>
            </div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Invoice Amount (৳)</label>
                        <input type="number" name="cost_amount" class="form-control" step="0.01" value="{{ old('cost_amount', $job->cost_amount) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Expense Amount (৳)</label>
                        <input type="number" name="expense_amount" class="form-control" step="0.01" value="{{ old('expense_amount', $job->expense_amount) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Payment Status</label>
                        <select name="is_paid" class="form-select">
                            <option value="0" {{ old('is_paid', $job->is_paid) == '0' ? 'selected' : '' }}>Unpaid</option>
                            <option value="1" {{ old('is_paid', $job->is_paid) == '1' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom:24px">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-card-text" style="color:var(--text-muted);margin-right:8px"></i>Notes</span>
            </div>
            <div class="card-body">
                <textarea name="notes" class="form-control">{{ old('notes', $job->notes) }}</textarea>
            </div>
        </div>

        <div style="display:flex;gap:12px;justify-content:flex-end">
            <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Save Changes
            </button>
        </div>
    </form>
</div>

@endsection