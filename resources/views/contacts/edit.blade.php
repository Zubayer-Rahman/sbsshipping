{{-- resources/views/contacts/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit ' . ($contact->type === 'client' ? 'Client' : 'Supplier'))
@section('page-title', 'Edit ' . ($contact->type === 'client' ? 'Client' : 'Supplier'))
@section('breadcrumb', 'Contacts / Edit / ' . $contact->contact_id)

@section('content')

<div>
    <div style="margin-bottom:20px">
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            Edit {{ $contact->type === 'client' ? 'Client' : 'Supplier' }}
            <span style="font-weight:400;font-size:16px;color:#888">({{ $contact->contact_id }})</span>
        </h2>
    </div>

    <form method="POST" action="{{ route('contacts.update', $contact) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="type" value="{{ $contact->type }}">

        {{-- ── SECTION 1: Business Name | Name | Mobile ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">
                <div class="contact-grid3">

                    <div class="form-group">
                        <label class="form-label">
                            Business Name:<span style="color:var(--danger)">*</span>
                        </label>
                        <input type="text" name="business_name"
                               class="form-control {{ $errors->has('business_name') ? 'is-invalid' : '' }}"
                               placeholder="Business / Company Name"
                               value="{{ old('business_name', $contact->business_name) }}">
                        @error('business_name')
                        <span style="font-size:12px;color:var(--danger)">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contact Person Name:</label>
                        <input type="text" name="name" class="form-control"
                               placeholder="Contact Person Name"
                               value="{{ old('name', $contact->name) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Mobile:
                            <span class="info-icon" title="Primary phone number">&#9432;</span>
                        </label>
                        <input type="text" name="mobile" class="form-control"
                               placeholder="01XXXXXXXXX"
                               value="{{ old('mobile', $contact->mobile) }}">
                    </div>

                </div>
            </div>
        </div>

        {{-- ── SECTION 2: Email | Tax Number | Address ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">
                <div class="contact-grid3">

                    <div class="form-group">
                        <label class="form-label">Email:</label>
                        <input type="email" name="email" class="form-control"
                               placeholder="email@example.com"
                               value="{{ old('email', $contact->email) }}">
                        @error('email')
                        <span style="font-size:12px;color:var(--danger)">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tax Number:</label>
                        <input type="text" name="tax_number" class="form-control"
                               placeholder="Tax / TIN Number"
                               value="{{ old('tax_number', $contact->tax_number) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Address:</label>
                        <input type="text" name="address" class="form-control"
                               placeholder="Full Address"
                               value="{{ old('address', $contact->address) }}">
                    </div>

                </div>
            </div>
        </div>

        {{-- ── SECTION 3: Pay Term | Opening Balance | Advance Balance ── --}}
        <div class="card" style="margin-bottom:28px">
            <div class="card-body" style="padding:24px">
                <div class="contact-grid3">

                    <div class="form-group">
                        <label class="form-label">
                            Pay Term:
                            <span class="info-icon" title="Number of days/months for payment">&#9432;</span>
                        </label>
                        <div style="display:flex;gap:8px">
                            <input type="number" name="pay_term_number" class="form-control"
                                   placeholder="e.g. 30" min="1"
                                   value="{{ old('pay_term_number', $contact->pay_term_number) }}" style="flex:1">
                            <select name="pay_term_type" class="form-select" style="flex:1">
                                <option value="">-- Type --</option>
                                <option value="days" {{ old('pay_term_type', $contact->pay_term_type) === 'days' ? 'selected' : '' }}>Days</option>
                                <option value="months" {{ old('pay_term_type', $contact->pay_term_type) === 'months' ? 'selected' : '' }}>Months</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Opening Balance:</label>
                        <div style="display:flex;align-items:center">
                            <span style="background:var(--body-bg);border:1px solid var(--border);
                                         border-right:none;padding:8px 12px;border-radius:6px 0 0 6px;
                                         font-size:13px;color:#666;font-weight:600">TK.</span>
                            <input type="number" step="0.01" name="opening_balance"
                                   class="form-control"
                                   placeholder="0.00"
                                   value="{{ old('opening_balance', $contact->opening_balance) }}"
                                   style="border-radius:0 6px 6px 0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Advance Balance:</label>
                        <div style="display:flex;align-items:center">
                            <span style="background:var(--body-bg);border:1px solid var(--border);
                                         border-right:none;padding:8px 12px;border-radius:6px 0 0 6px;
                                         font-size:13px;color:#666;font-weight:600">TK.</span>
                            <input type="number" step="0.01" name="advance_balance"
                                   class="form-control"
                                   placeholder="0.00"
                                   value="{{ old('advance_balance', $contact->advance_balance) }}"
                                   style="border-radius:0 6px 6px 0">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── BUTTONS ── --}}
        <div style="display:flex;justify-content:center;gap:12px;margin-bottom:32px">
            <a href="{{ route('contacts.index', $contact->type) }}"
               class="btn" style="background:#6c757d;color:#fff;min-width:100px;
                                  font-size:15px;padding:13px 28px">
                Cancel
            </a>
            <button type="submit" name="action" value="save"
                    class="btn" style="background:#7c3aed;color:#fff;min-width:100px;
                                       font-size:15px;padding:13px 28px;
                                       box-shadow:0 4px 14px rgba(124,58,237,.35)">
                Update
            </button>
        </div>

    </form>
</div>

@endsection

@push('styles')
<style>
    .contact-grid3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px 24px;
        align-items: start;
    }
    .is-invalid {
        border-color: var(--danger) !important;
    }
    .info-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 17px;
        height: 17px;
        background: var(--primary);
        color: #fff;
        border-radius: 50%;
        font-size: 11px;
        cursor: help;
        font-style: normal;
        margin-left: 4px;
        vertical-align: middle;
    }
    @media (max-width: 900px) {
        .contact-grid3 { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 600px) {
        .contact-grid3 { grid-template-columns: 1fr; }
    }
</style>
@endpush