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
                <div class="form-group">
                    <label class="form-label" style="font-weight:700">
                        JOB Number
                        <span style="font-weight:400;color:var(--text-muted);font-size:12px">
                            (No Need To Select Job No. If Office Expenses)
                        </span>
                    </label>

                    <div id="jobTrigger"
                        style="display:flex;align-items:center;gap:8px;
                            border:1.5px solid var(--border);border-radius:var(--radius-sm);
                            background:#fff;padding:0 12px;height:40px;cursor:pointer;
                            max-width:560px;transition:border-color .2s"
                        onclick="toggleJobDropdown(event)">
                        <i class="bi bi-search" style="color:var(--text-muted);font-size:13px;flex-shrink:0"></i>
                        <input type="text" id="jobSearch"
                            placeholder="Search or select job numbers..."
                            autocomplete="off"
                            style="border:none;outline:none;flex:1;font-size:13px;
                                  font-family:'Inter',sans-serif;background:transparent;
                                  cursor:text;color:var(--text-primary)"
                            onclick="event.stopPropagation();openJobDropdown()"
                            oninput="filterJobs(this.value)">
                        <i class="bi bi-chevron-down" id="jobChevron"
                            style="color:var(--text-muted);font-size:11px;flex-shrink:0;transition:transform .2s"></i>
                    </div>

                    <div id="jobTags" style="display:flex;flex-wrap:wrap;gap:5px;margin-top:7px"></div>

                    <input type="hidden" name="job_id" id="jobIdSingle">
                    <input type="hidden" name="job_ref_no" id="jobRefNo">

                    <div style="font-size:12px;color:var(--text-muted);margin-top:5px;
                            display:flex;align-items:center;gap:10px">
                        <span>Leave empty to autogenerate · Multiple jobs can be selected</span>
                        <span id="jobSelectedCount"
                            style="display:none;background:var(--primary);color:#fff;
                                 font-size:11px;font-weight:700;padding:2px 10px;
                                 border-radius:20px;white-space:nowrap">
                        </span>
                    </div>
                </div>

                {{-- Hidden checkbox pool to track selections --}}
                <div id="jobCheckboxPool" style="display:none">
                    @foreach(\App\Models\Job::orderBy('id', 'desc')->get() as $job)
                    <input type="checkbox"
                        class="job-check"
                        value="{{ $job->no ?? $job->id }}"
                        data-ref="{{ $job->job_no ?? $job->job_id }}"
                        data-client="{{ $job->client_name }}"
                        data-category="{{ $job->category }}"
                        data-invoice="{{ $job->invoice_value_usd }}"
                        data-imp-exp="{{ $job->imp_exp_value }}">
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
                    <div class="form-group">
                        <label class="form-label">Payment Account:</label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-cash-stack" style="color:var(--text-muted)"></i>
                            </span>
                            <select name="payment_account_id" required class="form-control">
                                @foreach(\App\Models\PaymentAccount::where('is_active', true)->orderBy('account_name')->get() as $acc)
                                <option value="{{ $acc->id }}" {{ old('payment_account_id') == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->account_name }} (Balance: ৳{{ number_format($acc->current_balance, 2) }})
                                </option>
                                @endforeach
                            </select>
                        </div>
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

{{-- Floating Dropdown Panel --}}
<div id="jobFloatingDropdown"
    style="display:none;position:fixed;background:#fff;
           border:1.5px solid var(--primary);border-radius:var(--radius-sm);
           box-shadow:0 8px 28px rgba(15,31,75,.14);
           max-height:380px;z-index:9999;flex-direction:column;overflow:hidden">

    <div style="display:flex;justify-content:space-between;align-items:center;
                padding:10px 14px;border-bottom:1px solid var(--border);
                background:var(--body-bg);flex-shrink:0">
        <span style="font-size:12px;font-weight:700;color:var(--text-primary)">
            Select Jobs (Multiple Allowed)
        </span>
        <button type="button" onclick="clearAllJobs()"
            style="font-size:12px;color:var(--danger);background:none;
                   border:none;cursor:pointer;font-weight:600;padding:0">
            Clear All
        </button>
    </div>

    <div id="jobVisualList" style="overflow-y:auto;flex:1;padding:4px">
        @foreach(\App\Models\Job::orderBy('id', 'desc')->get() as $job)
        <div class="job-visual-option"
            data-id="{{ $job->id }}"
            data-search="{{ strtolower(($job->job_id ?? $job->job_no) . ' ' . $job->client_name) }}"
            style="display:flex;align-items:center;gap:12px;padding:12px 14px;
                        cursor:pointer;border-bottom:1px solid var(--border);
                        transition:background .15s">

            {{-- Checkbox visual --}}
            <span class="job-visual-check"
                style="width:18px;height:18px;border:2px solid var(--border);
                             border-radius:4px;flex-shrink:0;display:flex;
                             align-items:center;justify-content:center;
                             transition:all .2s;background:#fff">
            </span>

            {{-- Job info --}}
            <div style="flex:1;min-width:0">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px">
                    <span style="font-size:13px;font-weight:700;color:var(--primary);
                                     font-family:'Inter',sans-serif">
                        {{ $job->job_no ?? $job->job_id }}
                    </span>
                    @if($job->category)
                    <span style="font-size:10px;padding:2px 6px;border-radius:10px;
                                         background:{{ str_contains(strtolower($job->category), 'import') ? '#dbeafe' : '#fef3c7' }};
                                         color:{{ str_contains(strtolower($job->category), 'import') ? '#1e40af' : '#92400e' }};
                                         font-weight:600;text-transform:uppercase">
                        {{ $job->category }}
                    </span>
                    @endif

                    @if($job->type)
                    <span style="font-size:10px;padding:2px 6px;border-radius:10px;
                                         background:{{ str_contains(strtolower($job->type), 'import') ? '#dbeafe' : '#fef3c7' }};
                                         color:{{ str_contains(strtolower($job->type    ), 'import') ? '#1e40af' : '#92400e' }};
                                         font-weight:600;text-transform:uppercase">
                        {{ $job->type }}
                    </span>
                    @endif
                </div>
                <div style="font-size:12px;color:var(--text-muted);
                                overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    👤 {{ $job->client_name ?? 'No client' }}
                </div>
            </div>

            {{-- Invoice Value --}}
            @if($job->invoice_value_usd)
            <div style="text-align:right;flex-shrink:0">
                <div style="font-size:11px;color:var(--text-muted)">Invoice</div>
                <div style="font-size:12px;font-weight:700;color:var(--text-primary)">
                    ${{ number_format($job->invoice_value_usd, 0) }}
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div id="jobNoResults"
        style="display:none;padding:20px;text-align:center;font-size:13px;
               color:var(--text-muted)">
        No jobs found matching your search.
    </div>
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

    #itemSuggestions div:hover {
        background: var(--primary-light);
        cursor: pointer
    }

    .job-visual-option:hover {
        background: var(--primary-light) !important;
    }

    .job-visual-option.selected {
        background: var(--primary-light) !important;
    }

    .job-visual-option.selected .job-visual-check {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
    }

    .job-visual-option.selected .job-visual-check::after {
        content: '✓';
        font-size: 11px;
        color: #fff;
        font-weight: 700;
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
    const floatDD = document.getElementById('jobFloatingDropdown');
    const trigger = document.getElementById('jobTrigger');
    const jobChevron = document.getElementById('jobChevron');
    const jobTags = document.getElementById('jobTags');
    const badge = document.getElementById('jobSelectedCount');
    let ddOpen = false;

    function isChecked(id) {
        const cb = document.querySelector(`#jobCheckboxPool .job-check[value="${id}"]`);
        return cb ? cb.checked : false;
    }

    function positionDD() {
        const r = trigger.getBoundingClientRect();
        const w = Math.max(trigger.offsetWidth, 420);
        floatDD.style.width = w + 'px';
        floatDD.style.left = r.left + 'px';
        const below = window.innerHeight - r.bottom;
        floatDD.style.top = below < 290 ?
            (r.top - 284) + 'px' :
            (r.bottom + 4) + 'px';
    }

    function openJobDropdown() {
        floatDD.style.display = 'flex';
        ddOpen = true;
        jobChevron.style.transform = 'rotate(180deg)';
        positionDD();
    }

    function closeJobDropdown() {
        floatDD.style.display = 'none';
        ddOpen = false;
        jobChevron.style.transform = 'rotate(0deg)';
    }

    function toggleJobDropdown(e) {
        if (e.target === document.getElementById('jobSearch')) return;
        ddOpen ? closeJobDropdown() : openJobDropdown();
    }

    document.addEventListener('click', e => {
        if (!trigger.contains(e.target) && !floatDD.contains(e.target)) closeJobDropdown();
    });
    ['scroll', 'resize'].forEach(ev =>
        window.addEventListener(ev, () => {
            if (ddOpen) positionDD();
        }, true)
    );

    // Click on visual row
    document.querySelectorAll('.job-visual-option').forEach(opt => {
        opt.addEventListener('click', function() {
            const id = this.dataset.id;
            const cb = document.querySelector(`#jobCheckboxPool .job-check[value="${id}"]`);
            if (!cb) return;
            cb.checked = !cb.checked;
            this.classList.toggle('selected', cb.checked);
            syncTags();
        });
    });

    // Filter
    function filterJobs(val) {
        openJobDropdown();
        const v = val.toLowerCase().trim();
        let any = false;
        document.querySelectorAll('.job-visual-option').forEach(opt => {
            const match = !v || opt.dataset.ref.toLowerCase().includes(v);
            opt.style.display = match ? 'flex' : 'none';
            if (match) any = true;
        });
        document.getElementById('jobNoResults').style.display = any ? 'none' : 'block';
    }

    // Sync tags, hidden inputs, and count badge
    function syncTags() {
        const checked = [...document.querySelectorAll('#jobCheckboxPool .job-check:checked')];
        jobTags.innerHTML = '';
        const refs = [],
            ids = [];

        checked.forEach(cb => {
            refs.push(cb.dataset.ref);
            ids.push(cb.value);

            const opt = document.querySelector(`.job-visual-option[data-id="${cb.value}"]`);
            if (opt) opt.classList.add('selected');

            const tag = document.createElement('span');
            tag.style.cssText = 'display:inline-flex;align-items:center;gap:5px;' +
                'background:var(--primary-light);color:var(--primary);' +
                'border:1px solid var(--primary);border-radius:20px;' +
                'padding:3px 10px;font-size:12px;font-weight:600;' +
                "font-family:'Inter',sans-serif";
            tag.innerHTML = cb.dataset.ref +
                `<button type="button" onclick="removeJob('${cb.value}')"
                style="background:none;border:none;cursor:pointer;
                       color:var(--primary);font-size:15px;line-height:1;padding:0">
                &times;
            </button>`;
            jobTags.appendChild(tag);
        });

        // Deselect visual rows no longer checked
        document.querySelectorAll('.job-visual-option').forEach(opt => {
            if (!ids.includes(opt.dataset.id)) opt.classList.remove('selected');
        });

        document.getElementById('jobIdSingle').value = ids[0] || '';
        document.getElementById('jobRefNo').value = refs.join(', ');

        // ── Update count badge ──
        const count = ids.length;
        badge.style.display = count > 0 ? 'inline-flex' : 'none';
        badge.textContent = count + (count === 1 ? ' job selected' : ' jobs selected');
    }

    function removeJob(id) {
        const cb = document.querySelector(`#jobCheckboxPool .job-check[value="${id}"]`);
        if (cb) {
            cb.checked = false;
            syncTags();
        }
    }

    function selectAllJobs() {
        document.querySelectorAll('.job-visual-option:not([style*="display: none"])').forEach(opt => {
            const cb = document.querySelector(`#jobCheckboxPool .job-check[value="${opt.dataset.id}"]`);
            if (cb) {
                cb.checked = true;
                opt.classList.add('selected');
            }
        });
        syncTags();
    }

    function clearAllJobs() {
        document.querySelectorAll('#jobCheckboxPool .job-check').forEach(cb => cb.checked = false);
        document.querySelectorAll('.job-visual-option').forEach(opt => opt.classList.remove('selected'));
        syncTags();
        document.getElementById('jobSearch').value = '';
        filterJobs('');
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