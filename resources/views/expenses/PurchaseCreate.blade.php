@extends('layouts.app')
@section('title','Add Purchase')
@section('page-title','Add Purchase')
@section('breadcrumb','Expenses / Purchases / Add Purchase')

@section('content')
<div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
        <h2 style="font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            Add Purchase
        </h2>
        <a href="{{ route('purchases.list') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> List Purchases
        </a>
    </div>

    <form method="POST" action="{{ route('purchases.store') }}" enctype="multipart/form-data" id="purchaseForm">
        @csrf

        {{-- ── SECTION 1: Header ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">
                <div class="pur-grid4">

                    {{-- Supplier --}}
                    <div class="form-group">
                        <label class="form-label">Supplier:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;gap:8px;align-items:center">
                            <div style="display:flex;align-items:center;border:1.5px solid var(--border);
                                    border-radius:var(--radius-sm);overflow:hidden;flex:1">
                                <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;
                                         display:flex;align-items:center;color:var(--text-muted)">
                                    <i class="bi bi-person"></i>
                                </span>
                                <select name="supplier_id" id="supplierSelect" class="form-select"
                                    style="border:none;border-radius:0;flex:1" required
                                    onchange="fillAddress(this)">
                                    <option value="">Please Select</option>
                                    @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}"
                                        data-address="{{ $s->address }}"
                                        data-mobile="{{ $s->mobile }}">
                                        {{ $s->business_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Purchase Date --}}
                    <div class="form-group">
                        <label class="form-label">Purchase Date:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-calendar3" style="color:var(--text-muted)"></i>
                            </span>
                            <input type="datetime-local" name="purchase_date" class="form-control"
                                style="border:none;border-radius:0;flex:1"
                                value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>

                    {{-- Pay Term --}}
                    <div class="form-group">
                        <label class="form-label">
                            Pay term:
                            <span class="pur-info">i</span>
                        </label>
                        <div style="display:flex;gap:8px">
                            <input type="number" name="pay_term_number" class="form-control"
                                placeholder="Pay term" style="flex:1" min="0">
                            <select name="pay_term_type" class="form-select" style="width:130px">
                                <option value="">Please Select</option>
                                <option value="Days">Days</option>
                                <option value="Months">Months</option>
                            </select>
                        </div>
                    </div>

                    {{-- Attach Document --}}
                    <div class="form-group">
                        <label class="form-label">Attach Document:</label>
                        <div style="display:flex;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <input type="text" id="purFileDisplay" placeholder="" readonly
                                class="form-control" style="border:none;border-radius:0;flex:1;background:#f8fafc;font-size:12px">
                            <label for="purDocFile"
                                style="display:inline-flex;align-items:center;gap:6px;padding:0 14px;
                                      background:#1a56db;color:#fff;font-size:12px;font-weight:600;
                                      cursor:pointer;white-space:nowrap">
                                <i class="bi bi-folder2-open"></i> Browse..
                            </label>
                            <input type="file" id="purDocFile" name="document" style="display:none"
                                accept=".pdf,.csv,.zip,.doc,.docx,.jpeg,.jpg,.png"
                                onchange="document.getElementById('purFileDisplay').value=this.files[0]?.name||''">
                        </div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:3px">Max File size: 5MB</div>
                        <div style="font-size:11px;color:var(--text-muted)">Allowed: .pdf .csv .zip .doc .docx .jpeg .jpg .png</div>
                    </div>

                </div>

                {{-- Address (auto-filled) --}}
                <div class="form-group" style="margin-top:14px">
                    <label class="form-label">Address:</label>
                    <input type="text" id="supplierAddress" class="form-control"
                        style="max-width:480px;background:#f8fafc" readonly
                        placeholder="Auto-filled from supplier">
                </div>

            </div>
        </div>

        {{-- ── SECTION 2: Items ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:20px 24px">

                {{-- Search bar --}}
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;flex-wrap:wrap">
                    <button type="button" onclick="document.getElementById('itemSearchInput').focus()"
                        style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;
                               border-radius:6px;background:#7c3aed;color:#fff;border:none;
                               font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif">
                        Import Item
                    </button>
                    <div style="position:relative;flex:1;max-width:500px">
                        <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px"></i>
                        <input type="text" id="itemSearchInput" placeholder="Enter Items name"
                            class="form-control" style="padding-left:36px"
                            oninput="searchItems(this.value)" autocomplete="off">
                        <div id="itemSuggestions"
                            style="display:none;position:absolute;top:calc(100%+4px);left:0;right:0;
                                background:#fff;border:1.5px solid var(--primary);border-radius:var(--radius-sm);
                                box-shadow:0 8px 24px rgba(15,31,75,.12);z-index:500;max-height:200px;overflow-y:auto">
                        </div>
                    </div>
                    <button type="button" onclick="addEmptyRow()"
                        style="display:inline-flex;align-items:center;gap:5px;padding:8px 16px;
                               border-radius:6px;background:transparent;color:var(--primary);
                               border:1.5px solid var(--primary);font-size:13px;font-weight:600;
                               cursor:pointer;font-family:'Inter',sans-serif;white-space:nowrap">
                        + Add new item
                    </button>
                </div>

                {{-- Items table --}}
                <div style="overflow-x:auto">
                    <table style="width:100%;min-width:900px;border-collapse:collapse;font-size:13px">
                        <thead>
                            <tr style="background:#28a745;color:#fff">
                                <th style="padding:10px 10px;text-align:center;width:36px">#</th>
                                <th style="padding:10px 10px;text-align:left">Item Name</th>
                                <th style="padding:10px 10px;text-align:center">Purchase Quantity</th>
                                <th style="padding:10px 10px;text-align:right">Unit Cost (Before Discount)</th>
                                <th style="padding:10px 10px;text-align:center">Discount Percent</th>
                                <th style="padding:10px 10px;text-align:right">Unit Cost (Before Tax)</th>
                                <th style="padding:10px 10px;text-align:right">Line Total</th>
                                <th style="padding:10px 10px;text-align:center">Profit Margin %</th>
                                <th style="padding:10px 10px;text-align:right">Unit Selling Price (Inc. tax)</th>
                                <th style="padding:10px 10px;text-align:center;width:40px">
                                    <i class="bi bi-trash"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            {{-- rows added by JS --}}
                        </tbody>
                    </table>
                </div>

                {{-- Totals --}}
                <div style="display:flex;justify-content:flex-end;margin-top:12px;gap:16px">
                    <div style="text-align:right;font-size:13px">
                        <div style="margin-bottom:4px;color:var(--text-muted)">
                            Total Items: <strong id="totalItemsDisplay" style="color:var(--text-primary)">0.00</strong>
                        </div>
                        <div style="color:var(--text-muted)">
                            Net Total Amount: <strong id="netTotalDisplay" style="color:var(--text-primary)">0.00</strong>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── SECTION 3: Add Payment ── --}}
        <div class="card" style="margin-bottom:24px">
            <div style="padding:16px 24px 14px;border-bottom:1px solid var(--border)">
                <span style="font-size:15px;font-weight:700;color:var(--text-primary)">Add payment</span>
            </div>
            <div class="card-body" style="padding:24px">

                <div style="font-size:13px;color:var(--text-muted);margin-bottom:14px">
                    Advance Balance: <strong>0</strong>
                </div>

                <div class="pur-grid3" style="margin-bottom:20px">

                    <div class="form-group">
                        <label class="form-label">Amount:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-cash" style="color:var(--text-muted)"></i>
                            </span>
                            <input type="number" name="payment_amount" id="paymentAmount"
                                class="form-control" style="border:none;border-radius:0;flex:1"
                                value="0.00" step="0.01" min="0">
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

                <div class="form-group" style="max-width:380px;margin-bottom:20px">
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

                <div class="form-group" style="margin-bottom:16px">
                    <label class="form-label">Payment note:</label>
                    <textarea name="payment_note" class="form-control"
                        style="min-height:80px;resize:vertical"
                        placeholder="Payment notes..."></textarea>
                </div>

                <hr style="border:none;border-top:1px solid var(--border);margin-bottom:12px">
                <div style="text-align:right;font-size:14px;font-weight:700;color:var(--text-primary)">
                    Purchase Total: TK. <span id="purchaseTotalDisplay">0.00</span>
                </div>

            </div>
        </div>

        <div style="display:flex;justify-content:center;margin-bottom:32px">
            <button type="submit" class="btn"
                style="background:#7c3aed;color:#fff;min-width:160px;
                       font-size:15px;font-weight:700;padding:13px 36px;
                       border-radius:8px;box-shadow:0 4px 14px rgba(124,58,237,.3);
                       font-family:'Inter',sans-serif">
                Save
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
        font-family: 'Inter', sans-serif !important;
    }

    .pur-grid4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px 20px;
        align-items: start;
    }

    .pur-grid3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px 20px;
        align-items: start;
    }

    .pur-info {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        background: var(--primary);
        color: #fff;
        border-radius: 50%;
        font-size: 10px;
        font-weight: 700;
        cursor: help;
        font-style: normal;
        vertical-align: middle;
        margin-left: 3px;
    }

    #itemsTableBody tr td {
        padding: 6px 8px;
        border-bottom: 1px solid var(--border);
    }

    #itemsTableBody input {
        font-size: 12px;
        padding: 5px 8px;
    }

    #itemSuggestions div:hover {
        background: var(--primary-light);
        cursor: pointer;
    }

    @media(max-width:1100px) {
        .pur-grid4 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width:900px) {
        .pur-grid3 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width:600px) {

        .pur-grid4,
        .pur-grid3 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let rowIndex = 0;

    // Auto-fill address from supplier
    function fillAddress(sel) {
        const opt = sel.options[sel.selectedIndex];
        document.getElementById('supplierAddress').value = opt.dataset.address || '';
    }

    // Add empty row
    function addEmptyRow(item = {}) {
        rowIndex++;
        const i = rowIndex;
        const tbody = document.getElementById('itemsTableBody');
        const tr = document.createElement('tr');
        tr.id = 'row-' + i;
        tr.innerHTML = `
        <td style="text-align:center;color:var(--text-muted)">${i}</td>
        <td>
            <input type="text" name="item_name[]" value="${item.item_name||''}"
                   class="form-control" style="min-width:140px" placeholder="Item name">
            <input type="hidden" name="item_code[]" value="${item.item_code||''}">
            <input type="hidden" name="unit[]"      value="${item.unit||'Nos (Nos)'}">
        </td>
        <td style="text-align:center">
            <input type="number" name="purchase_quantity[]" value="${item.qty||1}"
                   class="form-control row-qty" data-row="${i}"
                   style="width:80px;text-align:center" min="0.01" step="0.01">
        </td>
        <td style="text-align:right">
            <input type="number" name="unit_cost[]" value="${item.cost||0}"
                   class="form-control row-cost" data-row="${i}"
                   style="width:110px;text-align:right" min="0" step="0.01">
        </td>
        <td style="text-align:center">
            <input type="number" name="discount_percent[]" value="0"
                   class="form-control row-disc" data-row="${i}"
                   style="width:70px;text-align:center" min="0" max="100" step="0.01">
        </td>
        <td style="text-align:right">
            <input type="number" name="unit_cost_before_tax[]"
                   class="form-control row-cost-bt" data-row="${i}"
                   value="${item.cost||0}" style="width:110px;text-align:right" readonly>
        </td>
        <td style="text-align:right">
            <input type="number" name="line_total[]"
                   class="form-control row-lt" data-row="${i}"
                   value="${item.cost||0}" style="width:110px;text-align:right" readonly>
        </td>
        <td style="text-align:center">
            <input type="number" name="profit_margin[]" value="${item.margin||0}"
                   class="form-control row-margin" data-row="${i}"
                   style="width:70px;text-align:center" min="0" step="0.01">
        </td>
        <td style="text-align:right">
            <input type="number" name="unit_selling_price[]"
                   class="form-control row-sell" data-row="${i}"
                   value="${item.sell||0}" style="width:110px;text-align:right" step="0.01">
        </td>
        <td style="text-align:center">
            <button type="button" onclick="removeRow(${i})"
                    style="background:var(--danger);color:#fff;border:none;
                           border-radius:4px;padding:4px 8px;cursor:pointer;font-size:13px">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
        tbody.appendChild(tr);
        attachRowListeners(i);
        calcTotals();
    }

    function removeRow(i) {
        const tr = document.getElementById('row-' + i);
        if (tr) tr.remove();
        calcTotals();
    }

    function attachRowListeners(i) {
        const row = document.getElementById('row-' + i);
        const qty = row.querySelector('.row-qty');
        const cost = row.querySelector('.row-cost');
        const disc = row.querySelector('.row-disc');
        const mgn = row.querySelector('.row-margin');

        function recalcRow() {
            const q = parseFloat(qty.value) || 0;
            const c = parseFloat(cost.value) || 0;
            const d = parseFloat(disc.value) || 0;
            const m = parseFloat(mgn.value) || 0;
            const costBT = c * (1 - d / 100);
            const lt = costBT * q;
            const sellP = costBT * (1 + m / 100);
            row.querySelector('.row-cost-bt').value = costBT.toFixed(2);
            row.querySelector('.row-lt').value = lt.toFixed(2);
            row.querySelector('.row-sell').value = sellP.toFixed(2);
            calcTotals();
        }

        [qty, cost, disc, mgn].forEach(el => el.addEventListener('input', recalcRow));
    }

    function calcTotals() {
        let totalItems = 0,
            netTotal = 0;
        document.querySelectorAll('.row-qty').forEach(el => totalItems += parseFloat(el.value) || 0);
        document.querySelectorAll('.row-lt').forEach(el => netTotal += parseFloat(el.value) || 0);
        document.getElementById('totalItemsDisplay').textContent = totalItems.toFixed(2);
        document.getElementById('netTotalDisplay').textContent = netTotal.toFixed(2);
        document.getElementById('purchaseTotalDisplay').textContent = netTotal.toFixed(2);
    }

    // Item search
    let searchTimeout;

    function searchItems(val) {
        clearTimeout(searchTimeout);
        const box = document.getElementById('itemSuggestions');
        if (!val.trim()) {
            box.style.display = 'none';
            return;
        }
        searchTimeout = setTimeout(() => {
            fetch(`/purchases/items/search?q=${encodeURIComponent(val)}`)
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
                        d.textContent = item.item_name + (item.item_code ? ' - ' + item.item_code : '');
                        d.addEventListener('click', () => {
                            addEmptyRow({
                                item_name: item.item_name,
                                item_code: item.item_code,
                                unit: item.unit,
                                cost: item.exc_tax || 0,
                                sell: item.billing_exc_tax || 0,
                                qty: 1,
                            });
                            document.getElementById('itemSearchInput').value = '';
                            box.style.display = 'none';
                        });
                        box.appendChild(d);
                    });
                    box.style.display = 'block';
                });
        }, 250);
    }

    // Close suggestions on outside click
    document.addEventListener('click', e => {
        if (!e.target.closest('#itemSearchInput') && !e.target.closest('#itemSuggestions')) {
            document.getElementById('itemSuggestions').style.display = 'none';
        }
    });
</script>
@endpush