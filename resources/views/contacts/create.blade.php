{{-- resources/views/contacts/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add ' . ($type === 'client' ? 'Client' : 'Supplier'))
@section('page-title', 'Add ' . ($type === 'client' ? 'Client' : 'Supplier'))
@section('breadcrumb', 'Contacts / ' . ($type === 'client' ? 'Clients' : 'Suppliers') . ' / Add')

@section('content')

<div>
    <div style="margin-bottom:20px">
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            Add new {{ $type === 'client' ? 'Client' : 'Supplier' }}
        </h2>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div style="background:#d4edda;color:#155724;padding:12px 20px;border-radius:6px;
                    margin-bottom:16px;border:1px solid #c3e6cb;display:flex;
                    justify-content:space-between;align-items:center">
            <span>{{ session('success') }}</span>
            <span onclick="this.parentElement.remove()" style="cursor:pointer;font-size:18px">&times;</span>
        </div>
    @endif

    <form method="POST" action="{{ route('contacts.store') }}">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

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
                               value="{{ old('business_name') }}">
                        @error('business_name')
                        <span style="font-size:12px;color:var(--danger)">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Contact Person Name:
                        </label>
                        <input type="text" name="name" class="form-control"
                               placeholder="Contact Person Name"
                               value="{{ old('name') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Mobile:
                            <span class="info-icon" title="Primary phone number">&#9432;</span>
                        </label>
                        <input type="text" name="mobile" class="form-control"
                               placeholder="01XXXXXXXXX"
                               value="{{ old('mobile') }}">
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
                               value="{{ old('email') }}">
                        @error('email')
                        <span style="font-size:12px;color:var(--danger)">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tax Number:</label>
                        <input type="text" name="tax_number" class="form-control"
                               placeholder="Tax / TIN Number"
                               value="{{ old('tax_number') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Address:</label>
                        <input type="text" name="address" class="form-control"
                               placeholder="Full Address"
                               value="{{ old('address') }}">
                    </div>

                </div>
            </div>
        </div>

        {{-- ── SECTION 3: Pay Term | Opening Balance | Advance Balance ── --}}
        <div class="card" style="margin-bottom:28px">
            <div class="card-body" style="padding:24px">

                <div class="contact-grid3" style="margin-bottom:20px">

                    {{-- Pay Term --}}
                    <div class="form-group">
                        <label class="form-label">
                            Pay Term:
                            <span class="info-icon" title="Number of days/months for payment">&#9432;</span>
                        </label>
                        <div style="display:flex;gap:8px">
                            <input type="number" name="pay_term_number" class="form-control"
                                   placeholder="e.g. 30" min="1"
                                   value="{{ old('pay_term_number') }}" style="flex:1">
                            <select name="pay_term_type" class="form-select" style="flex:1">
                                <option value="">-- Type --</option>
                                <option value="days" {{ old('pay_term_type') === 'days' ? 'selected' : '' }}>Days</option>
                                <option value="months" {{ old('pay_term_type') === 'months' ? 'selected' : '' }}>Months</option>
                            </select>
                        </div>
                    </div>

                    {{-- Opening Balance --}}
                    <div class="form-group">
                        <label class="form-label">Opening Balance:</label>
                        <div style="display:flex;align-items:center">
                            <span style="background:var(--body-bg);border:1px solid var(--border);
                                         border-right:none;padding:8px 12px;border-radius:6px 0 0 6px;
                                         font-size:13px;color:#666;font-weight:600">TK.</span>
                            <input type="number" step="0.01" name="opening_balance"
                                   class="form-control"
                                   placeholder="0.00"
                                   value="{{ old('opening_balance', '0.00') }}"
                                   style="border-radius:0 6px 6px 0">
                        </div>
                    </div>

                    {{-- Advance Balance --}}
                    <div class="form-group">
                        <label class="form-label">Advance Balance:</label>
                        <div style="display:flex;align-items:center">
                            <span style="background:var(--body-bg);border:1px solid var(--border);
                                         border-right:none;padding:8px 12px;border-radius:6px 0 0 6px;
                                         font-size:13px;color:#666;font-weight:600">TK.</span>
                            <input type="number" step="0.01" name="advance_balance"
                                   class="form-control"
                                   placeholder="0.00"
                                   value="{{ old('advance_balance', '0.00') }}"
                                   style="border-radius:0 6px 6px 0">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── BUTTONS ── --}}
        <div style="display:flex;justify-content:center;gap:12px;margin-bottom:32px">
            <button type="submit" name="action" value="save_and_add"
                    class="btn" style="background:#e91e8c;color:#fff;min-width:200px;
                                       font-size:15px;padding:13px 28px;
                                       box-shadow:0 4px 14px rgba(233,30,140,.35)">
                Save And Add Another
            </button>
            <button type="submit" name="action" value="save"
                    class="btn" style="background:#7c3aed;color:#fff;min-width:100px;
                                       font-size:15px;padding:13px 28px;
                                       box-shadow:0 4px 14px rgba(124,58,237,.35)">
                Save
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
        .contact-grid3 {
            grid-template-columns: 1fr 1fr;
        }
    }
    @media (max-width: 600px) {
        .contact-grid3 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush