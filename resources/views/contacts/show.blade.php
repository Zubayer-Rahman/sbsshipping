{{-- resources/views/contacts/show.blade.php --}}
@extends('layouts.app')

@section('title', 'View ' . $contact->contact_id)
@section('page-title', $contact->business_name ?? $contact->name)
@section('breadcrumb', 'Contacts / View / ' . $contact->contact_id)

@section('content')

<div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            {{ $contact->business_name ?? $contact->name }}
            <span style="font-weight:400;font-size:16px;color:#888">({{ $contact->contact_id }})</span>
        </h2>
        <div style="display:flex;gap:10px">
            <a href="{{ route('contacts.edit', $contact) }}"
                class="btn" style="background:#f0ad4e;color:#fff;padding:8px 20px;
                                  border-radius:6px;font-size:13px;font-weight:600">
                &#9998; Edit
            </a>
            <a href="{{ route('contacts.index', $contact->type) }}"
                class="btn" style="background:#6c757d;color:#fff;padding:8px 20px;
                                  border-radius:6px;font-size:13px;font-weight:600">
                &larr; Back
            </a>
        </div>
    </div>

    <div class="card" style="margin-bottom:28px">
        <div class="card-body" style="padding:24px">
            <div class="detail-grid">

                <div class="detail-row">
                    <span class="detail-label">Contact ID</span>
                    <span class="detail-value">{{ $contact->contact_id }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Type</span>
                    <span class="detail-value">
                        <span style="background:{{ $contact->type === 'supplier' ? 'var(--primary)' : '#28a745' }};
                                     color:#fff;padding:3px 12px;border-radius:12px;font-size:12px">
                            {{ ucfirst($contact->type) }}
                        </span>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Business Name</span>
                    <span class="detail-value">{{ $contact->business_name ?? '—' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Name</span>
                    <span class="detail-value">{{ $contact->name ?? '—' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value">{{ $contact->email ?? '—' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Mobile</span>
                    <span class="detail-value">{{ $contact->mobile ?? '—' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Tax Number</span>
                    <span class="detail-value">{{ $contact->tax_number ?? '—' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Pay Term</span>
                    <span class="detail-value">{{ $contact->pay_term_display ?: '—' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Opening Balance</span>
                    <span class="detail-value">TK. {{ number_format($contact->opening_balance, 2) }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Advance Balance</span>
                    <span class="detail-value">TK. {{ number_format($contact->advance_balance, 2) }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Total Purchase Due</span>
                    <span class="detail-value" style="font-weight:700;color:var(--danger)">
                        TK. {{ number_format($contact->total_purchase_due, 2) }}
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Purchase Return Due</span>
                    <span class="detail-value">TK. {{ number_format($contact->total_purchase_return_due, 2) }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Address</span>
                    <span class="detail-value">{{ $contact->address ?? '—' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        <span style="background:{{ $contact->is_active ? '#28a745' : 'var(--danger)' }};
                                     color:#fff;padding:3px 12px;border-radius:12px;font-size:12px">
                            {{ $contact->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Added On</span>
                    <span class="detail-value">{{ $contact->created_at->format('d/m/Y h:i A') }}</span>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }

    .detail-row {
        display: flex;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
    }

    .detail-row:nth-child(odd) {
        background: #fafbfc;
    }

    .detail-label {
        font-weight: 700;
        font-size: 13px;
        color: #555;
        min-width: 180px;
    }

    .detail-value {
        font-size: 13px;
        color: var(--text-primary);
    }

    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush