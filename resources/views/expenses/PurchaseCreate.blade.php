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
                <div class="pur-grid3">

                    {{-- Supplier --}}
                    <div class="form-group">
                        <label class="form-label">Supplier:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center;color:var(--text-muted)">
                                <i class="bi bi-person"></i>
                            </span>
                            <select name="supplier_id" class="form-select" style="border:none;border-radius:0;flex:1" required>
                                <option value="">Please Select</option>
                                @foreach($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->business_name }}</option>
                                @endforeach
                            </select>
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
                        <div style="font-size:11px;color:var(--text-muted);margin-top:3px">Max File size: 5MB · Allowed: .pdf .csv .zip .doc .docx .jpeg .jpg .png</div>
                    </div>

                </div>

                {{-- JOB Number multi-select --}}
                <div class="form-group" style="margin-top:18px">
                    <label class="form-label" style="font-weight:700">
                        JOB Number
                        <span style="font-weight:400;color:var(--text-muted);font-size:12px">
                            (Link this purchase to one or more jobs)
                        </span>
                    </label>

                    <div id="purJobTrigger"
                        style="display:flex;align-items:center;gap:8px;
                            border:1.5px solid var(--border);border-radius:var(--radius-sm);
                            background:#fff;padding:0 12px;height:40px;cursor:pointer;
                            max-width:560px;transition:border-color .2s"
                        onclick="togglePurJobDD(event)">
                        <i class="bi bi-search" style="color:var(--text-muted);font-size:13px;flex-shrink:0"></i>
                        <input type="text" id="purJobSearch"
                            placeholder="Search or select job numbers..."
                            autocomplete="off"
                            style="border:none;outline:none;flex:1;font-size:13px;
                                  font-family:'Inter',sans-serif;background:transparent;
                                  cursor:text;color:var(--text-primary)"
                            onclick="event.stopPropagation();openPurJobDD()"
                            oninput="filterPurJobs(this.value)">
                        <i class="bi bi-chevron-down" id="purJobChevron"
                            style="color:var(--text-muted);font-size:11px;flex-shrink:0;transition:transform .2s"></i>
                    </div>

                    <div id="purJobTags" style="display:flex;flex-wrap:wrap;gap:5px;margin-top:7px"></div>
                    <input type="hidden" name="job_id" id="purJobIdSingle">
                    <input type="hidden" name="job_ref_no" id="purJobRefNo">

                    <div style="font-size:12px;color:var(--text-muted);margin-top:5px;display:flex;align-items:center;gap:10px">
                        <span>Multiple jobs can be selected</span>
                        <span id="purJobCount" style="display:none;background:var(--primary);color:#fff;
                                                  font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px"></span>
                    </div>
                </div>

                {{-- Hidden job checkboxes --}}
                <div id="purJobPool" style="display:none">
                    @foreach($jobs as $job)
                    @php $ref = $job->job_no ?? $job->job_id; @endphp
                    <input type="checkbox" name="job_ids[]" value="{{ $job->id }}"
                        data-ref="{{ $ref }}" class="pur-job-check">
                    @endforeach
                </div>

            </div>
        </div>

        {{-- ── SECTION 2: Items ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:20px 24px">

                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;flex-wrap:wrap">
                    <div style="position:relative;flex:1;max-width:460px">
                        <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px"></i>
                        <input type="text" id="itemSearchInput" placeholder="Enter Item name to search..."
                            class="form-control" style="padding-left:36px"
                            oninput="searchItems(this.value)" autocomplete="off">
                        <div id="itemSuggestions"
                            style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;
                                background:#fff;border:1.5px solid var(--primary);
                                border-radius:var(--radius-sm);box-shadow:0 8px 24px rgba(15,31,75,.12);
                                z-index:500;max-height:200px;overflow-y:auto">
                        </div>
                    </div>
                    <button type="button" onclick="addEmptyRow()"
                        style="display:inline-flex;align-items:center;gap:5px;padding:8px 16px;
                               border-radius:6px;background:transparent;color:var(--primary);
                               border:1.5px solid var(--primary);font-size:13px;font-weight:600;
                               cursor:pointer;white-space:nowrap;font-family:'Inter',sans-serif">
                        + Add new item
                    </button>
                </div>

                <div style="overflow-x:auto">
                    <table style="width:100%;min-width:500px;border-collapse:collapse;font-size:13px">
                        <thead>
                            <tr style="background:#28a745;color:#fff">
                                <th style="padding:10px 12px;width:36px;text-align:center">#</th>
                                <th style="padding:10px 12px;text-align:left">Item Name</th>
                                <th style="padding:10px 12px;text-align:center;width:120px">Purchase Quantity</th>
                                <th style="padding:10px 12px;text-align:right;width:150px">Purchase Amount</th>
                                <th style="padding:10px 12px;text-align:center;width:50px">
                                    <i class="bi bi-trash"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody"></tbody>
                    </table>
                </div>

                <div style="display:flex;justify-content:flex-end;margin-top:12px">
                    <div style="font-size:13px;text-align:right">
                        <div style="margin-bottom:4px;color:var(--text-muted)">
                            Total Items: <strong id="totalItemsDisplay" style="color:var(--text-primary)">0.00</strong>
                        </div>
                        <div style="color:var(--text-muted)">
                            Net Total Amount: <strong id="netTotalDisplay" style="color:var(--primary)">0.00</strong>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── SECTION 3: Payment ── --}}
        <div class="card" style="margin-bottom:24px">
            <div style="padding:16px 24px 14px;border-bottom:1px solid var(--border)">
                <span style="font-size:15px;font-weight:700;color:var(--text-primary)">Add payment</span>
            </div>
            <div class="card-body" style="padding:24px">
                <div class="pur-grid3" style="margin-bottom:20px">
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
                        style="min-height:80px;resize:vertical" placeholder="Payment notes..."></textarea>
                </div>
                <hr style="border:none;border-top:1px solid var(--border);margin-bottom:12px">
                <div style="text-align:right;font-size:14px;font-weight:700;color:var(--text-primary)">
                    Purchase Total: TK. <span id="purchaseTotalDisplay">0.00</span>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:center;margin-bottom:32px">
            <button type="submit" class="btn"
                style="background:#7c3aed;color:#fff;min-width:160px;font-size:15px;font-weight:700;
                       padding:13px 36px;border-radius:8px;box-shadow:0 4px 14px rgba(124,58,237,.3);
                       font-family:'Inter',sans-serif">
                Save
            </button>
        </div>

    </form>
</div>

{{-- Floating job dropdown --}}
<div id="purJobFloatDD"
    style="display:none;position:fixed;background:#fff;border:1.5px solid var(--primary);
            border-radius:var(--radius-sm);box-shadow:0 8px 28px rgba(15,31,75,.14);
            max-height:280px;z-index:9999;flex-direction:column;overflow:hidden">
    <div style="display:flex;justify-content:space-between;align-items:center;
                padding:8px 14px;border-bottom:1px solid var(--border);
                background:var(--body-bg);flex-shrink:0">
        <span style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em">Select Jobs</span>
        <div style="display:flex;gap:12px">
            <button type="button" onclick="selectAllPurJobs()"
                style="font-size:12px;color:var(--primary);background:none;border:none;cursor:pointer;font-weight:700;padding:0">Select All</button>
            <button type="button" onclick="clearAllPurJobs()"
                style="font-size:12px;color:var(--danger);background:none;border:none;cursor:pointer;font-weight:700;padding:0">Clear</button>
        </div>
    </div>
    <div id="purJobVisualList" style="overflow-y:auto;flex:1">
        @foreach($jobs as $job)
        @php $ref = $job->job_no ?? $job->job_id; @endphp
        <div class="pur-job-opt" data-ref="{{ $ref }}" data-id="{{ $job->id }}"
            style="display:flex;align-items:center;gap:10px;padding:9px 14px;
                    cursor:pointer;border-bottom:1px solid var(--border);transition:background .12s">
            <span class="pur-job-chk"
                style="width:16px;height:16px;border:2px solid var(--border);border-radius:3px;
                         flex-shrink:0;display:flex;align-items:center;justify-content:center;
                         transition:all .15s;background:#fff"></span>
            <span style="font-size:13px;font-weight:600;color:var(--primary);font-family:'Inter',sans-serif">{{ $ref }}</span>
            @if(!empty($job->client_name))
            <span style="font-size:12px;color:var(--text-muted);margin-left:auto;max-width:180px;
                         overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $job->client_name }}</span>
            @endif
        </div>
        @endforeach
    </div>
    <div id="purJobNoResults" style="display:none;padding:20px;text-align:center;font-size:13px;color:var(--text-muted)">No jobs found</div>
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

    .pur-grid3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px 20px;
        align-items: start
    }

    #itemsTableBody tr td {
        padding: 7px 10px;
        border-bottom: 1px solid var(--border)
    }

    #itemsTableBody input {
        font-size: 13px;
        padding: 6px 8px
    }

    .pur-job-opt:hover {
        background: var(--primary-light) !important
    }

    .pur-job-opt.selected {
        background: var(--primary-light) !important
    }

    .pur-job-opt.selected .pur-job-chk {
        background: var(--primary) !important;
        border-color: var(--primary) !important
    }

    .pur-job-opt.selected .pur-job-chk::after {
        content: '✓';
        font-size: 11px;
        color: #fff;
        font-weight: 700
    }

    #itemSuggestions div:hover {
        background: var(--primary-light);
        cursor: pointer
    }

    @media(max-width:900px) {
        .pur-grid3 {
            grid-template-columns: repeat(2, 1fr)
        }
    }

    @media(max-width:600px) {
        .pur-grid3 {
            grid-template-columns: 1fr
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // ── Item rows ─────────────────────────────────────────────────────────────────
    let rowIdx = 0;

    function addEmptyRow(item = {}) {
        rowIdx++;
        const i = rowIdx;
        const tr = document.createElement('tr');
        tr.id = 'prow-' + i;
        tr.innerHTML = `
        <td style="text-align:center;color:var(--text-muted)">${i}</td>
        <td>
            <input type="text" name="item_name[]" value="${item.item_name||''}"
                   class="form-control" placeholder="Item name" style="min-width:180px">
            <input type="hidden" name="item_code[]" value="${item.item_code||''}">
            <input type="hidden" name="unit[]"      value="${item.unit||'Nos (Nos)'}">
        </td>
        <td>
            <input type="number" name="purchase_quantity[]" value="${item.qty||1}"
                   class="form-control prow-qty" data-row="${i}"
                   style="width:90px;text-align:center" min="0.01" step="0.01">
        </td>
        <td>
            <input type="number" name="unit_cost[]" value="${item.cost||0}"
                   class="form-control prow-cost" data-row="${i}"
                   style="width:120px;text-align:right" min="0" step="0.01">
            <input type="hidden" name="discount_percent[]"    value="0">
            <input type="hidden" name="unit_cost_before_tax[]" value="${item.cost||0}">
            <input type="hidden" name="line_total[]"           class="prow-lt" data-row="${i}" value="${item.cost||0}">
            <input type="hidden" name="profit_margin[]"        value="0">
            <input type="hidden" name="unit_selling_price[]"   value="${item.sell||0}">
        </td>
        <td style="text-align:center">
            <button type="button" onclick="removePRow(${i})"
                    style="background:var(--danger);color:#fff;border:none;border-radius:4px;
                           padding:5px 10px;cursor:pointer;font-size:13px">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
        document.getElementById('itemsTableBody').appendChild(tr);
        attachPRowListeners(i);
        calcPTotals();
    }

    function removePRow(i) {
        const tr = document.getElementById('prow-' + i);
        if (tr) tr.remove();
        calcPTotals();
    }

    function attachPRowListeners(i) {
        const row = document.getElementById('prow-' + i);
        const qty = row.querySelector('.prow-qty');
        const cost = row.querySelector('.prow-cost');
        const lt = row.querySelector('.prow-lt');

        function recalc() {
            const q = parseFloat(qty.value) || 0;
            const c = parseFloat(cost.value) || 0;
            const total = q * c;
            lt.value = total.toFixed(2);
            calcPTotals();
        }
        qty.addEventListener('input', recalc);
        cost.addEventListener('input', recalc);
    }

    function calcPTotals() {
        let ti = 0,
            nt = 0;
        document.querySelectorAll('.prow-qty').forEach(e => ti += parseFloat(e.value) || 0);
        document.querySelectorAll('.prow-lt').forEach(e => nt += parseFloat(e.value) || 0);
        document.getElementById('totalItemsDisplay').textContent = ti.toFixed(2);
        document.getElementById('netTotalDisplay').textContent = nt.toFixed(2);
        document.getElementById('purchaseTotalDisplay').textContent = nt.toFixed(2);
    }

    // ── Item search ───────────────────────────────────────────────────────────────
    let sTimeout;

    function searchItems(val) {
        clearTimeout(sTimeout);
        const box = document.getElementById('itemSuggestions');
        if (!val.trim()) {
            box.style.display = 'none';
            return;
        }
        sTimeout = setTimeout(() => {
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
                                qty: 1
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
    document.addEventListener('click', e => {
        if (!e.target.closest('#itemSearchInput') && !e.target.closest('#itemSuggestions'))
            document.getElementById('itemSuggestions').style.display = 'none';
    });

    // ── Job dropdown ──────────────────────────────────────────────────────────────
    const purFloatDD = document.getElementById('purJobFloatDD');
    const purTrigger = document.getElementById('purJobTrigger');
    const purChevron = document.getElementById('purJobChevron');
    const purTags = document.getElementById('purJobTags');
    const purBadge = document.getElementById('purJobCount');
    let purDDOpen = false;

    function purPosDd() {
        const r = purTrigger.getBoundingClientRect();
        const w = Math.max(purTrigger.offsetWidth, 420);
        purFloatDD.style.width = w + 'px';
        purFloatDD.style.left = r.left + 'px';
        purFloatDD.style.top = (window.innerHeight - r.bottom < 290 ? r.top - 284 : r.bottom + 4) + 'px';
    }

    function openPurJobDD() {
        purFloatDD.style.display = 'flex';
        purDDOpen = true;
        purChevron.style.transform = 'rotate(180deg)';
        purPosDd();
    }

    function closePurJobDD() {
        purFloatDD.style.display = 'none';
        purDDOpen = false;
        purChevron.style.transform = 'rotate(0deg)';
    }

    function togglePurJobDD(e) {
        if (e.target === document.getElementById('purJobSearch')) return;
        purDDOpen ? closePurJobDD() : openPurJobDD();
    }

    document.addEventListener('click', e => {
        if (!purTrigger.contains(e.target) && !purFloatDD.contains(e.target)) closePurJobDD();
    });
    ['scroll', 'resize'].forEach(ev => window.addEventListener(ev, () => {
        if (purDDOpen) purPosDd();
    }, true));

    document.querySelectorAll('.pur-job-opt').forEach(opt => {
        opt.addEventListener('click', function() {
            const id = this.dataset.id;
            const cb = document.querySelector(`#purJobPool .pur-job-check[value="${id}"]`);
            if (!cb) return;
            cb.checked = !cb.checked;
            this.classList.toggle('selected', cb.checked);
            syncPurTags();
        });
    });

    function filterPurJobs(val) {
        openPurJobDD();
        const v = val.toLowerCase().trim();
        let any = false;
        document.querySelectorAll('.pur-job-opt').forEach(opt => {
            const match = !v || opt.dataset.ref.toLowerCase().includes(v);
            opt.style.display = match ? 'flex' : 'none';
            if (match) any = true;
        });
        document.getElementById('purJobNoResults').style.display = any ? 'none' : 'block';
    }

    function syncPurTags() {
        const checked = [...document.querySelectorAll('#purJobPool .pur-job-check:checked')];
        purTags.innerHTML = '';
        const refs = [],
            ids = [];
        checked.forEach(cb => {
            refs.push(cb.dataset.ref);
            ids.push(cb.value);
            const opt = document.querySelector(`.pur-job-opt[data-id="${cb.value}"]`);
            if (opt) opt.classList.add('selected');
            const tag = document.createElement('span');
            tag.style.cssText = 'display:inline-flex;align-items:center;gap:5px;background:var(--primary-light);color:var(--primary);border:1px solid var(--primary);border-radius:20px;padding:3px 10px;font-size:12px;font-weight:600';
            tag.innerHTML = cb.dataset.ref + `<button type="button" onclick="removePurJob('${cb.value}')" style="background:none;border:none;cursor:pointer;color:var(--primary);font-size:15px;line-height:1;padding:0">&times;</button>`;
            purTags.appendChild(tag);
        });
        document.querySelectorAll('.pur-job-opt').forEach(opt => {
            if (!ids.includes(opt.dataset.id)) opt.classList.remove('selected');
        });
        document.getElementById('purJobIdSingle').value = ids[0] || '';
        document.getElementById('purJobRefNo').value = refs.join(', ');
        purBadge.style.display = ids.length ? 'inline-flex' : 'none';
        purBadge.textContent = ids.length + (ids.length === 1 ? ' job selected' : ' jobs selected');
    }

    function removePurJob(id) {
        const cb = document.querySelector(`#purJobPool .pur-job-check[value="${id}"]`);
        if (cb) {
            cb.checked = false;
            syncPurTags();
        }
    }

    function selectAllPurJobs() {
        document.querySelectorAll('.pur-job-opt:not([style*="display: none"])').forEach(opt => {
            const cb = document.querySelector(`#purJobPool .pur-job-check[value="${opt.dataset.id}"]`);
            if (cb) {
                cb.checked = true;
                opt.classList.add('selected');
            }
        });
        syncPurTags();
    }

    function clearAllPurJobs() {
        document.querySelectorAll('#purJobPool .pur-job-check').forEach(cb => cb.checked = false);
        document.querySelectorAll('.pur-job-opt').forEach(o => o.classList.remove('selected'));
        syncPurTags();
        document.getElementById('purJobSearch').value = '';
        filterPurJobs('');
    }
</script>
@endpush