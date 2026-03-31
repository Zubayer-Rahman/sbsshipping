@extends('layouts.app')

@section('title', 'Edit Item')
@section('page-title', 'Edit Item')
@section('breadcrumb', 'Items / Edit Item')

@section('content')

<div style="max-width:1200px">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            Edit Item
        </h2>
        <a href="{{ route('items.list') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form method="POST" action="{{ route('items.update', $item) }}">
        @csrf @method('PUT')

        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">
                <div class="item-grid3">
                    <div class="form-group">
                        <label class="form-label">Item Name:<span style="color:var(--danger)">*</span></label>
                        <input type="text" name="item_name" class="form-control"
                               value="{{ old('item_name', $item->item_name) }}">
                        @error('item_name')<span style="font-size:12px;color:var(--danger)">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Item Code: <span class="info-icon" title="Unique identifier">&#9432;</span></label>
                        <input type="text" name="item_code" class="form-control"
                               value="{{ old('item_code', $item->item_code) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Unit:<span style="color:var(--danger)">*</span></label>
                        <select name="unit" class="form-select">
                            @foreach(['Nos (Nos)','Kg (Kilograms)','g (Grams)','L (Litres)','ml (Millilitres)','m (Metres)','cm (Centimetres)','Box','Pcs (Pieces)','Set','Pair','Pack','Dozen','Carton','Container','Hour','Day'] as $u)
                                <option value="{{ $u }}" {{ old('unit',$item->unit)==$u?'selected':'' }}>{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom:6px;min-height:54px">
            <div class="card-body" style="padding:16px 24px"></div>
        </div>

        <div class="card" style="margin-bottom:28px">
            <div class="card-body" style="padding:24px">
                <div class="item-grid3" style="margin-bottom:20px">
                    <div class="form-group">
                        <label class="form-label">Applicable Tax:</label>
                        <select name="applicable_tax" class="form-select">
                            @foreach(['None','5%','10%','15%','VAT'] as $t)
                                <option value="{{ $t }}" {{ old('applicable_tax',$item->applicable_tax)==$t?'selected':'' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Selling Price Tax Type:<span style="color:var(--danger)">*</span></label>
                        <select name="selling_price_tax_type" class="form-select">
                            <option value="Exclusive" {{ old('selling_price_tax_type',$item->selling_price_tax_type)=='Exclusive'?'selected':'' }}>Exclusive</option>
                            <option value="Inclusive" {{ old('selling_price_tax_type',$item->selling_price_tax_type)=='Inclusive'?'selected':'' }}>Inclusive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Item Type:<span style="color:var(--danger)">*</span></label>
                        <select name="item_type" class="form-select">
                            @foreach(['Single','Combo','Service'] as $t)
                                <option value="{{ $t }}" {{ old('item_type',$item->item_type)==$t?'selected':'' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pricing-header">
                    <div class="ph-cell ph-green">Default Purchase Price</div>
                    <div class="ph-cell ph-green">x Margin(%)</div>
                    <div class="ph-cell ph-green">Billing Amount</div>
                </div>
                <div class="pricing-body">
                    <div class="pricing-col" style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div class="form-group">
                            <label class="form-label" style="font-size:12px">Exc. tax:<span style="color:var(--danger)">*</span></label>
                            <input type="number" name="exc_tax" id="excTax" class="form-control"
                                   step="0.01" value="{{ old('exc_tax', $item->exc_tax) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size:12px">Inc. tax:<span style="color:var(--danger)">*</span></label>
                            <input type="number" name="inc_tax" id="incTax" class="form-control"
                                   step="0.01" value="{{ old('inc_tax', $item->inc_tax) }}">
                        </div>
                    </div>
                    <div class="pricing-col">
                        <div class="form-group">
                            <label class="form-label" style="font-size:12px">&nbsp;</label>
                            <input type="number" name="margin" id="margin" class="form-control"
                                   step="0.01" value="{{ old('margin', $item->margin ?? '0.00') }}">
                        </div>
                    </div>
                    <div class="pricing-col">
                        <div class="form-group">
                            <label class="form-label" style="font-size:12px">Exc. Tax</label>
                            <input type="number" name="billing_exc_tax" id="billingExcTax" class="form-control"
                                   step="0.01" value="{{ old('billing_exc_tax', $item->billing_exc_tax) }}"
                                   style="background:var(--body-bg)" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:center;gap:12px;margin-bottom:32px">
            <a href="{{ route('items.list') }}" class="btn btn-outline" style="min-width:120px;font-size:15px;padding:13px 28px">
                Cancel
            </a>
            <button type="submit" class="btn"
                    style="background:#7c3aed;color:#fff;min-width:120px;font-size:15px;padding:13px 28px;
                           box-shadow:0 4px 14px rgba(124,58,237,.35)">
                Save Changes
            </button>
        </div>
    </form>
</div>

@endsection

@push('styles')
<style>
.item-grid3 { display:grid; grid-template-columns:repeat(3,1fr); gap:16px 24px; align-items:start; }
.info-icon { display:inline-flex;align-items:center;justify-content:center;width:17px;height:17px;background:var(--primary);color:#fff;border-radius:50%;font-size:11px;cursor:help;font-style:normal;margin-left:4px;vertical-align:middle; }
.pricing-header { display:grid; grid-template-columns:2fr 1fr 1fr; margin-bottom:0; border-radius:6px 6px 0 0; overflow:hidden; }
.ph-cell { background:#3cb371;color:#fff;font-size:13px;font-weight:700;padding:10px 14px;border-right:1px solid rgba(255,255,255,.2);display:flex;align-items:center;gap:4px; }
.ph-cell:last-child { border-right:none; }
.pricing-body { display:grid;grid-template-columns:2fr 1fr 1fr;border:1px solid var(--border);border-top:none;border-radius:0 0 6px 6px;padding:16px;background:var(--card-bg);gap:0 16px; }
@media(max-width:900px){.item-grid3{grid-template-columns:1fr 1fr}.pricing-header,.pricing-body{grid-template-columns:1fr}}
@media(max-width:600px){.item-grid3{grid-template-columns:1fr}}
</style>
@endpush

@push('scripts')
<script>
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
</script>
@endpush