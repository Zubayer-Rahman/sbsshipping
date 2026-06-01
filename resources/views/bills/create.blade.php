@extends('layouts.app')
@section('title','Add Bill')
@section('page-title','Add Bill')
@section('breadcrumb','Bill / Add Bill')

@section('content')
<div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
        <h2 style="font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">Add Bill</h2>
        <a href="{{ route('bills.list') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> All Bills</a>
    </div>

    <form method="POST" action="{{ route('bills.store') }}" id="billForm">
        @csrf

        {{-- ── SECTION 1: Business Location ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:18px 24px">
                <div style="display:flex;align-items:center;gap:8px;max-width:420px">
                    <span style="padding:0 10px;border:1.5px solid var(--border);border-right:none;
                             border-radius:var(--radius-sm) 0 0 var(--radius-sm);height:40px;
                             display:flex;align-items:center;background:var(--body-bg)">
                        <i class="bi bi-geo-alt" style="color:var(--text-muted)"></i>
                    </span>
                    <select class="form-select" style="border-radius:0 var(--radius-sm) var(--radius-sm) 0;flex:1">
                        <option value="BL0001">SBS Shipping (BL0001)</option>
                    </select>
                    <span class="info-dot" title="Business Location">i</span>
                </div>
            </div>
        </div>

        {{-- ── SECTION 2: Client + Dates ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">
                <div class="bill-grid4">

                    {{-- Client --}}
                    <div class="form-group">
                        <label class="form-label">Client:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;gap:8px;align-items:center">
                            <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;flex:1">
                                <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                    <i class="bi bi-person" style="color:var(--text-muted)"></i>
                                </span>
                                <select name="client_id" id="clientSelect" class="form-select" style="border:none;border-radius:0;flex:1" required onchange="loadClientInfo(this.value)">
                                    <option value="">(Select Client)</option>
                                    @foreach($clients as $c)
                                    <option value="{{ $c->id }}"
                                        data-name="{{ $c->business_name }}"
                                        data-address="{{ $c->address }}"
                                        data-mobile="{{ $c->mobile }}"
                                        data-balance="{{ $c->advance_balance ?? 0 }}">
                                        ({{ $c->contact_id ?? 'C'.$c->id }}) {{ $c->business_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="clientBalance" style="display:none;font-size:12px;color:var(--danger);margin-top:4px;font-weight:600"></div>
                    </div>

                    {{-- Pay term --}}
                    <div class="form-group">
                        <label class="form-label">Pay term: <span class="info-dot" title="Payment terms">i</span></label>
                        <div style="display:flex;gap:8px">
                            <input type="number" name="pay_term_number" class="form-control" placeholder="Pay term" style="width:100px" min="0">
                            <select name="pay_term_type" class="form-select">
                                <option value="">Please Select</option>
                                <option value="Days">Days</option>
                                <option value="Months">Months</option>
                            </select>
                        </div>
                    </div>

                    {{-- Billing Date --}}
                    <div class="form-group">
                        <label class="form-label">Billing Date:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-calendar3" style="color:var(--text-muted)"></i>
                            </span>
                            <input type="datetime-local" name="billing_date" class="form-control"
                                style="border:none;border-radius:0;flex:1"
                                value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label class="form-label">Status:<span style="color:var(--danger)">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="">Please Select</option>
                            <option value="Final" selected>Final</option>
                            <option value="Draft">Draft</option>
                        </select>
                    </div>

                </div>

                {{-- Row 2: Addresses + Job Number --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px 20px;margin-top:16px" id="clientDetails" style="display:none">
                    <div class="form-group">
                        <label class="form-label">Billing Address:</label>
                        <div id="billingAddress" style="font-size:13px;color:var(--text-primary);padding:8px 12px;background:var(--body-bg);border-radius:var(--radius-sm);min-height:60px;line-height:1.6"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Shipping Address:</label>
                        <div id="shippingAddress" style="font-size:13px;color:var(--text-primary);padding:8px 12px;background:var(--body-bg);border-radius:var(--radius-sm);min-height:60px;line-height:1.6"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Job Number:</label>
                        <input type="text" name="job_number" class="form-control" placeholder="Job Number">
                    </div>
                </div>

            </div>
        </div>

        {{-- ── SECTION 3: Items ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:20px 24px">

                {{-- Item table --}}
                <div style="overflow-x:auto;margin-bottom:14px">
                    <table style="width:100%;min-width:800px;border-collapse:collapse;font-size:13px">
                        <thead>
                            <tr style="background:var(--body-bg)">
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:center;width:36px;font-size:11px;text-transform:uppercase;color:var(--text-muted)">#</th>
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:left;font-size:11px;text-transform:uppercase;color:var(--text-muted)">Item</th>
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:left;font-size:11px;text-transform:uppercase;color:var(--text-muted)">Description</th>
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:center;font-size:11px;text-transform:uppercase;color:var(--text-muted);width:100px">Quantity</th>
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:right;font-size:11px;text-transform:uppercase;color:var(--text-muted);width:120px">Amount</th>
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:right;font-size:11px;text-transform:uppercase;color:var(--text-muted);width:120px">Subtotal</th>
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:center;width:40px">
                                    <span style="font-size:18px;font-weight:700;color:var(--text-muted)">✕</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="billItemsBody"></tbody>
                    </table>
                </div>

                {{-- Totals row --}}
                <div style="display:flex;justify-content:flex-end;padding:8px 12px;font-size:13px;gap:24px">
                    <span style="color:var(--text-muted)">Items: <strong id="totalItemsDisp">0.00</strong></span>
                    <span style="color:var(--text-muted)">Total: <strong id="totalDisp" style="color:var(--primary)">0.00</strong></span>
                </div>

                {{-- Item search --}}
                <div style="display:flex;align-items:center;gap:10px;margin-top:4px">
                    <div style="position:relative;flex:1;max-width:500px">
                        <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px"></i>
                        <input type="text" id="billItemSearch" placeholder="Enter Items name"
                            class="form-control" style="padding-left:36px"
                            oninput="searchBillItems(this.value)" autocomplete="off">
                        <div id="billItemSuggestions"
                            style="display:none;position:absolute;top:calc(100%+4px);left:0;right:0;
                                background:#fff;border:1.5px solid var(--primary);border-radius:var(--radius-sm);
                                box-shadow:0 8px 24px rgba(15,31,75,.12);z-index:500;max-height:200px;overflow-y:auto">
                        </div>
                    </div>
                    <button type="button" onclick="addBillRow()"
                        style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;
                               border-radius:6px;background:transparent;color:var(--primary);
                               border:1.5px solid var(--primary);font-size:13px;font-weight:600;cursor:pointer">
                        <i class="bi bi-plus-lg"></i> Add new item
                    </button>
                </div>

            </div>
        </div>

        {{-- ── SECTION 4: Discount + Tax + Notes ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px">

                    <div class="form-group">
                        <label class="form-label">Discount Type:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;gap:8px">
                            <span class="info-dot">i</span>
                            <select name="discount_type" id="discountType" class="form-select" style="flex:1" onchange="calcBillTotals()">
                                <option value="Percentage">Percentage</option>
                                <option value="Fixed">Fixed</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Discount Amount:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;gap:8px">
                            <span class="info-dot">i</span>
                            <input type="number" name="discount_amount" id="discountAmount"
                                class="form-control" style="flex:1" value="0.00" step="0.01" min="0"
                                oninput="calcBillTotals()">
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:6px;justify-content:flex-end">
                        <div style="text-align:right;font-size:14px;font-weight:600;color:var(--text-primary)">
                            Discount Amount:(-) <span id="discountDisplay">0.00</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Order Tax:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;gap:8px">
                            <span class="info-dot">i</span>
                            <select name="order_tax" id="orderTax" class="form-select" style="flex:1" onchange="calcBillTotals()">
                                <option value="None" data-rate="0">None</option>
                                <option value="5%" data-rate="5">5%</option>
                                <option value="10%" data-rate="10">10%</option>
                                <option value="15%" data-rate="15">15%</option>
                                <option value="VAT" data-rate="15">VAT (15%)</option>
                            </select>
                        </div>
                    </div>

                    <div></div>

                    <div style="display:flex;flex-direction:column;gap:6px;justify-content:flex-end">
                        <div style="text-align:right;font-size:14px;font-weight:600;color:var(--text-primary)">
                            Order Tax:(+) <span id="taxDisplay">0.00</span>
                        </div>
                    </div>

                </div>

                <div class="form-group" style="margin-top:16px">
                    <label class="form-label">Billing note</label>
                    <textarea name="billing_note" class="form-control" style="min-height:80px;resize:vertical" placeholder="Billing note..."></textarea>
                </div>

            </div>
        </div>

        {{-- ── SECTION 5: Total Payable ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:16px 24px;display:flex;align-items:center;justify-content:flex-end;gap:16px">
                <div style="font-size:15px;font-weight:700;color:var(--text-primary)">
                    Total Payable: TK. <span id="totalPayableDisp">0.00</span>
                </div>
            </div>
        </div>

        {{-- ── SECTION 6: Add Payment ── --}}
        <div class="card" style="margin-bottom:24px">
            <div style="padding:16px 24px 14px;border-bottom:1px solid var(--border)">
                <span style="font-size:15px;font-weight:700;color:var(--text-primary)">Add payment</span>
            </div>
            <div class="card-body" style="padding:24px">

                <div style="font-size:13px;color:var(--text-muted);margin-bottom:16px">
                    Advance Balance: TK. <strong id="advanceBalance">0.00</strong>
                </div>

                <div class="bill-grid3" style="margin-bottom:18px">
                    <div class="form-group">
                        <label class="form-label">Amount:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-cash" style="color:var(--text-muted)"></i>
                            </span>
                            <input type="number" name="payment_amount" class="form-control"
                                style="border:none;border-radius:0;flex:1" value="0.00" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Paid on:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-calendar3" style="color:var(--text-muted)"></i>
                            </span>
                            <input type="datetime-local" name="paid_on" class="form-control"
                                style="border:none;border-radius:0;flex:1"
                                value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Payment Method:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-credit-card" style="color:var(--text-muted)"></i>
                            </span>
                            <select name="payment_method" class="form-select" style="border:none;border-radius:0;flex:1">
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="bKash">bKash</option>
                                <option value="Nagad">Nagad</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="max-width:380px;margin-bottom:18px">
                    <label class="form-label">Payment Account:</label>
                    <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                        <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                            <i class="bi bi-cash-stack" style="color:var(--text-muted)"></i>
                        </span>
                        <select name="payment_account" class="form-select" style="border:none;border-radius:0;flex:1">
                            <option value="None">None</option>
                            <option value="Cash in Hand">Cash in Hand</option>
                            <option value="Bank Account">Bank Account</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Payment note:</label>
                    <textarea name="payment_note" class="form-control" style="min-height:80px;resize:vertical" placeholder="Payment notes..."></textarea>
                </div>

            </div>
        </div>

        {{-- Buttons --}}
        <div style="display:flex;justify-content:center;gap:14px;margin-bottom:32px">
            <button type="submit" name="action" value="save" class="btn"
                style="background:#7c3aed;color:#fff;min-width:120px;font-size:15px;font-weight:700;
                       padding:13px 32px;border-radius:8px;box-shadow:0 4px 14px rgba(124,58,237,.3)">
                Save
            </button>
            <button type="submit" name="action" value="save_print" class="btn"
                style="background:#10b981;color:#fff;min-width:160px;font-size:15px;font-weight:700;
                       padding:13px 32px;border-radius:8px;box-shadow:0 4px 14px rgba(16,185,129,.3)">
                Save and print
            </button>
        </div>

    </form>
</div>
@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    body,
    input,
    select,
    textarea,
    button {
        font-family: 'Inter', sans-serif !important
    }

    .bill-grid4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px 20px;
        align-items: start
    }

    .bill-grid3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px 20px;
        align-items: start
    }

    .info-dot {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 17px;
        height: 17px;
        background: var(--primary);
        color: #fff;
        border-radius: 50%;
        font-size: 10px;
        font-weight: 700;
        cursor: help;
        font-style: normal;
        vertical-align: middle;
        margin-left: 3px;
        flex-shrink: 0
    }

    #billItemsBody tr td {
        padding: 7px 10px;
        border-bottom: 1px solid var(--border)
    }

    #billItemsBody input {
        font-size: 13px;
        padding: 5px 8px
    }

    #billItemSuggestions div:hover {
        background: var(--primary-light);
        cursor: pointer
    }

    @media(max-width:1100px) {
        .bill-grid4 {
            grid-template-columns: repeat(2, 1fr)
        }
    }

    @media(max-width:900px) {
        .bill-grid3 {
            grid-template-columns: repeat(2, 1fr)
        }
    }

    @media(max-width:600px) {

        .bill-grid4,
        .bill-grid3 {
            grid-template-columns: 1fr
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let billRowIdx = 0;
    let billSubTotal = 0;

    // Load client info
    function loadClientInfo(id) {
        if (!id) {
            document.getElementById('clientDetails').style.display = 'none';
            document.getElementById('clientBalance').style.display = 'none';
            return;
        }
        const opt = document.querySelector(`#clientSelect option[value="${id}"]`);
        if (!opt) return;
        const name = opt.dataset.name || '';
        const address = opt.dataset.address || '';
        const mobile = opt.dataset.mobile || '';
        const balance = parseFloat(opt.dataset.balance || 0);

        document.getElementById('billingAddress').innerHTML = `<strong>${name}</strong><br>${address}<br>Mobile: ${mobile}`;
        document.getElementById('shippingAddress').innerHTML = `<strong>${name}</strong><br>${address}`;
        document.getElementById('clientDetails').style.display = 'grid';

        const balEl = document.getElementById('clientBalance');
        if (balance > 0) {
            balEl.textContent = `Client: TK. ${balance.toLocaleString('en-BD', {minimumFractionDigits:2})}`;
            balEl.style.display = 'block';
        } else {
            balEl.style.display = 'none';
        }
    }

    // Add empty bill row
    function addBillRow(item = {}) {
        billRowIdx++;
        const i = billRowIdx;
        const tr = document.createElement('tr');
        tr.id = 'brow-' + i;
        tr.innerHTML = `
        <td style="text-align:center;color:var(--text-muted)">${i}</td>
        <td>
            <input type="text" name="item_name[]" value="${item.item_name||''}"
                   class="form-control" style="min-width:160px" placeholder="Item name">
            <input type="hidden" name="item_code[]" value="${item.item_code||''}">
            <input type="hidden" name="unit[]" value="${item.unit||'Nos'}">
        </td>
        <td>
            <input type="text" name="description[]" class="form-control"
                   placeholder="Description..." style="min-width:160px">
        </td>
        <td>
            <input type="number" name="quantity[]" value="${item.qty||1}"
                   class="form-control brow-qty" data-row="${i}"
                   style="width:80px;text-align:center" min="0.01" step="0.01">
        </td>
        <td>
            <input type="number" name="unit_price[]" value="${item.price||0}"
                   class="form-control brow-price" data-row="${i}"
                   style="width:110px;text-align:right" min="0" step="0.01">
            <input type="hidden" name="item_discount[]" value="0">
            <input type="hidden" name="item_tax[]" value="0">
        </td>
        <td>
            <input type="number" name="subtotal_display[]"
                   class="form-control brow-sub" data-row="${i}"
                   value="${item.price||0}" style="width:110px;text-align:right" readonly>
        </td>
        <td style="text-align:center">
            <button type="button" onclick="removeBillRow(${i})"
                    style="background:var(--danger);color:#fff;border:none;border-radius:4px;
                           padding:5px 10px;cursor:pointer;font-size:13px">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
        document.getElementById('billItemsBody').appendChild(tr);
        attachBillRowListeners(i);
        calcBillTotals();
    }

    function removeBillRow(i) {
        const tr = document.getElementById('brow-' + i);
        if (tr) tr.remove();
        calcBillTotals();
    }

    function attachBillRowListeners(i) {
        const row = document.getElementById('brow-' + i);
        const qty = row.querySelector('.brow-qty');
        const price = row.querySelector('.brow-price');
        const sub = row.querySelector('.brow-sub');

        function recalc() {
            const q = parseFloat(qty.value) || 0;
            const p = parseFloat(price.value) || 0;
            sub.value = (q * p).toFixed(2);
            calcBillTotals();
        }
        qty.addEventListener('input', recalc);
        price.addEventListener('input', recalc);
    }

    function calcBillTotals() {
        let ti = 0,
            st = 0;
        document.querySelectorAll('.brow-qty').forEach(e => ti += parseFloat(e.value) || 0);
        document.querySelectorAll('.brow-sub').forEach(e => st += parseFloat(e.value) || 0);
        billSubTotal = st;

        // Discount
        const discType = document.getElementById('discountType').value;
        const discAmt = parseFloat(document.getElementById('discountAmount').value) || 0;
        const discVal = discType === 'Percentage' ? (st * discAmt / 100) : discAmt;
        document.getElementById('discountDisplay').textContent = discVal.toFixed(2);

        // Tax
        const taxSel = document.getElementById('orderTax');
        const taxRate = parseFloat(taxSel.options[taxSel.selectedIndex]?.dataset.rate || 0);
        const taxVal = (st - discVal) * taxRate / 100;
        document.getElementById('taxDisplay').textContent = taxVal.toFixed(2);

        // Total
        const total = st - discVal + taxVal;
        document.getElementById('totalItemsDisp').textContent = ti.toFixed(2);
        document.getElementById('totalDisp').textContent = total.toFixed(2);
        document.getElementById('totalPayableDisp').textContent = total.toFixed(2);
    }

    // Item search
    let billSearchTimeout;

    function searchBillItems(val) {
        clearTimeout(billSearchTimeout);
        const box = document.getElementById('billItemSuggestions');
        if (!val.trim()) {
            box.style.display = 'none';
            return;
        }
        billSearchTimeout = setTimeout(() => {
            fetch(`/bills/items/search?q=${encodeURIComponent(val)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.length) {
                        box.style.display = 'none';
                        return;
                    }
                    box.innerHTML = '';
                    data.forEach(item => {
                        const d = document.createElement('div');
                        d.style.cssText = 'padding:9px 14px;font-size:13px;border-bottom:1px solid var(--border)';
                        d.textContent = item.item_name + (item.item_code ? ' ' + item.item_code : '');
                        d.addEventListener('click', () => {
                            addBillRow({
                                item_name: item.item_name,
                                item_code: item.item_code,
                                unit: item.unit,
                                price: item.billing_exc_tax || 0,
                                qty: 1
                            });
                            document.getElementById('billItemSearch').value = '';
                            box.style.display = 'none';
                        });
                        box.appendChild(d);
                    });
                    box.style.display = 'block';
                });
        }, 250);
    }
    document.addEventListener('click', e => {
        if (!e.target.closest('#billItemSearch') && !e.target.closest('#billItemSuggestions'))
            document.getElementById('billItemSuggestions').style.display = 'none';
    });
</script>
@endpush