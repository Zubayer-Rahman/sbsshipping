@extends('layouts.app')

@section('title', 'Add New Item')
@section('page-title', 'Add New Item')
@section('breadcrumb', 'Items / Add Item')

@section('content')

<div >
    <div style="margin-bottom:20px">
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            Add new item
        </h2>
    </div>

    <form method="POST" action="{{ route('items.store') }}">
        @csrf

        {{-- ── SECTION 1: Item Name | Item Code | Unit ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">
                <div class="item-grid3">

                    <div class="form-group">
                        <label class="form-label">
                            Item Name:<span style="color:var(--danger)">*</span>
                        </label>
                        <input type="text" name="item_name" class="form-control {{ $errors->has('item_name') ? 'is-invalid' : '' }}"
                            placeholder="Product Name"
                            value="{{ old('item_name') }}">
                        @error('item_name')
                        <span style="font-size:12px;color:var(--danger)">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Item Code:
                            <span class="info-icon" title="A unique code to identify this item">&#9432;</span>
                        </label>
                        <input type="text" name="item_code" class="form-control"
                            placeholder="Item Code"
                            value="{{ old('item_code') }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Unit:<span style="color:var(--danger)">*</span>
                        </label>
                        <div style="display:flex;gap:8px;align-items:center">
                            <select name="unit" class="form-select" style="flex:1">
                                @foreach(['Nos (Nos)','Kg (Kilograms)','g (Grams)','L (Litres)','ml (Millilitres)','m (Metres)','cm (Centimetres)','Box','Pcs (Pieces)','Set','Pair','Pack','Dozen','Carton','Container','Hour','Day'] as $u)
                                <option value="{{ $u }}" {{ old('unit','Nos (Nos)') == $u ? 'selected':'' }}>{{ $u }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="add-unit-btn" title="Add new unit"
                                style="width:34px;height:38px;border-radius:50%;
                                           background:var(--primary);color:#fff;border:none;
                                           font-size:20px;cursor:pointer;display:flex;
                                           align-items:center;justify-content:center;
                                           flex-shrink:0;transition:background .2s"
                                onmouseover="this.style.background='var(--primary-dark)'"
                                onmouseout="this.style.background='var(--primary)'">
                                +
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── SECTION 2: (empty middle section as per screenshot) ── --}}
        <div class="card" style="margin-bottom:6px;min-height:54px">
            <div class="card-body" style="padding:16px 24px"></div>
        </div>

        {{-- ── SECTION 3: Tax | Selling Price Type | Item Type + Pricing Grid ── --}}
        <div class="card" style="margin-bottom:28px">
            <div class="card-body" style="padding:24px">

                {{-- Row: Applicable Tax | Selling Price Tax Type | Item Type --}}
                <div class="item-grid3" style="margin-bottom:20px">

                    <div class="form-group">
                        <label class="form-label">Applicable Tax:</label>
                        <select name="applicable_tax" class="form-select item-select-arrow">
                            <option value="None" {{ old('applicable_tax','None')=='None'  ?'selected':'' }}>None</option>
                            <option value="5%" {{ old('applicable_tax')=='5%'           ?'selected':'' }}>5%</option>
                            <option value="10%" {{ old('applicable_tax')=='10%'          ?'selected':'' }}>10%</option>
                            <option value="15%" {{ old('applicable_tax')=='15%'          ?'selected':'' }}>15%</option>
                            <option value="VAT" {{ old('applicable_tax')=='VAT'          ?'selected':'' }}>VAT</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Selling Price Tax Type:<span style="color:var(--danger)">*</span>
                        </label>
                        <select name="selling_price_tax_type" class="form-select item-select-arrow">
                            <option value="Exclusive" {{ old('selling_price_tax_type','Exclusive')=='Exclusive'?'selected':'' }}>Exclusive</option>
                            <option value="Inclusive" {{ old('selling_price_tax_type')=='Inclusive'           ?'selected':'' }}>Inclusive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Item Type:<span style="color:var(--danger)">*</span>
                            <span class="info-icon" title="Single: one product. Combo: bundle of products.">&#9432;</span>
                        </label>
                        <select name="item_type" class="form-select item-select-arrow">
                            <option value="Single" {{ old('item_type','Single')=='Single'?'selected':'' }}>Single</option>
                            <option value="Combo" {{ old('item_type')=='Combo'          ?'selected':'' }}>Combo</option>
                            <option value="Service" {{ old('item_type')=='Service'        ?'selected':'' }}>Service</option>
                        </select>
                    </div>

                </div>

                {{-- Pricing header row --}}
                <div class="pricing-header">
                    <div class="ph-cell ph-green">Default Purchase Price</div>
                    <div class="ph-cell ph-green">x Margin(%)
                        <span class="info-icon-white" title="Profit margin percentage">&#9432;</span>
                    </div>
                    <div class="ph-cell ph-green">Billing Amount</div>
                </div>

                {{-- Pricing sub-labels + inputs --}}
                <div class="pricing-body">

                    {{-- Default Purchase Price: Exc. tax + Inc. tax --}}
                    <div class="pricing-col" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div class="form-group">
                            <label class="form-label" style="font-size:12px">
                                Exc. tax:<span style="color:var(--danger)">*</span>
                            </label>
                            <input type="number" name="exc_tax" id="excTax"
                                class="form-control" placeholder="Exc. tax"
                                step="0.01" value="{{ old('exc_tax') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size:12px">
                                Inc. tax:<span style="color:var(--danger)">*</span>
                            </label>
                            <input type="number" name="inc_tax" id="incTax"
                                class="form-control" placeholder="Inc. tax"
                                step="0.01" value="{{ old('inc_tax') }}">
                        </div>
                    </div>

                    {{-- Margin --}}
                    <div class="pricing-col">
                        <div class="form-group">
                            <label class="form-label" style="font-size:12px">&nbsp;</label>
                            <input type="number" name="margin" id="margin"
                                class="form-control" placeholder="0.00"
                                step="0.01" value="{{ old('margin', '0.00') }}">
                        </div>
                    </div>

                    {{-- Billing Amount: Exc. Tax (auto-calc) --}}
                    <div class="pricing-col">
                        <div class="form-group">
                            <label class="form-label" style="font-size:12px">Exc. Tax</label>
                            <input type="number" name="billing_exc_tax" id="billingExcTax"
                                class="form-control" placeholder="Exc. tax"
                                step="0.01" value="{{ old('billing_exc_tax') }}"
                                style="background:var(--body-bg)" readonly>
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
    .item-grid3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px 24px;
        align-items: start;
    }

    .is-invalid {
        border-color: var(--danger) !important;
    }

    /* Select with down-arrow styling */
    .item-select-arrow {
        appearance: auto;
        -webkit-appearance: auto;
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

    .info-icon-white {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        background: rgba(255, 255, 255, .3);
        color: #fff;
        border-radius: 50%;
        font-size: 10px;
        cursor: help;
        font-style: normal;
        margin-left: 5px;
    }

    /* Pricing grid header */
    .pricing-header {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 0;
        margin-bottom: 0;
        border-radius: 6px 6px 0 0;
        overflow: hidden;
    }

    .ph-cell {
        background: #3cb371;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 14px;
        border-right: 1px solid rgba(255, 255, 255, .2);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .ph-cell:last-child {
        border-right: none;
    }

    /* Pricing body */
    .pricing-body {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        gap: 0 0;
        border: 1px solid var(--border);
        border-top: none;
        border-radius: 0 0 6px 6px;
        padding: 16px;
        background: var(--card-bg);
        gap: 0 16px;
    }

    .pricing-col {
        padding: 0;
    }

    @media (max-width: 900px) {
        .item-grid3 {
            grid-template-columns: 1fr 1fr;
        }

        .pricing-header,
        .pricing-body {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .item-grid3 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-calculate Billing Exc. Tax = Exc. Tax × (1 + Margin/100)
    const excTax = document.getElementById('excTax');
    const margin = document.getElementById('margin');
    const billingExcTax = document.getElementById('billingExcTax');

    function calcBilling() {
        const exc = parseFloat(excTax.value) || 0;
        const mgn = parseFloat(margin.value) || 0;
        billingExcTax.value = (exc * (1 + mgn / 100)).toFixed(2);
    }
    excTax.addEventListener('input', calcBilling);
    margin.addEventListener('input', calcBilling);

    // Auto-fill Inc. Tax from Exc. Tax (simple, no tax applied yet)
    excTax.addEventListener('input', function() {
        const incTaxField = document.getElementById('incTax');
        if (!incTaxField.value) {
            incTaxField.value = this.value;
        }
    });
</script>
@endpush