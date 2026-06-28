@extends('layouts.app')
@section('title','Edit Expense')
@section('page-title','Edit Expense')
@section('breadcrumb','Expenses / Edit Expense')

@section('content')
<div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            Edit Expense
            @if($expense->expense_ref)
            <span style="font-size:14px;font-weight:500;color:var(--primary);margin-left:8px">
                — {{ $expense->expense_ref }}
            </span>
            @endif
        </h2>
        <a href="{{ route('expenses.list') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <form method="POST" action="{{ route('expenses.update', $expense) }}" enctype="multipart/form-data" id="expenseForm">
        @csrf @method('PUT')

        {{-- ── SECTION 1 ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">
                <div class="exp-grid3" style="margin-bottom:20px">

                    <div class="form-group">
                        <label class="form-label">Expense Category:</label>
                        <select name="expense_category" id="expCat" class="form-select exp-select">
                            <option value="">Please Select</option>
                            <option value="Job Expense" {{ $expense->expense_category == 'Job Expense'    ? 'selected' : '' }}>Job Expense</option>
                            <option value="Office Expense" {{ $expense->expense_category == 'Office Expense' ? 'selected' : '' }}>Office Expense</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Sub category:</label>
                        <select name="sub_category" id="subCat" class="form-select exp-select">
                            <option value="">Please Select</option>
                            {{-- Populated by JS on load --}}
                            @if($expense->sub_category)
                            <option value="{{ $expense->sub_category }}" selected>{{ $expense->sub_category }}</option>
                            @endif
                        </select>
                    </div>

                </div>

                {{-- JOB Number --}}
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
                        onmouseenter="this.style.borderColor='var(--primary)'"
                        onmouseleave="this.style.borderColor=ddOpen?'var(--primary)':'var(--border)'"
                        onclick="toggleJobDropdown(event)">
                        <i class="bi bi-search" style="color:var(--text-muted);font-size:13px;flex-shrink:0"></i>
                        <input type="text" id="jobSearch"
                            placeholder="Search or select job numbers..."
                            autocomplete="off"
                            style="border:none;outline:none;flex:1;font-size:13px;
                                  font-family:inherit;background:transparent;cursor:text;color:var(--text-primary)"
                            onclick="event.stopPropagation();openJobDropdown()"
                            oninput="filterJobs(this.value)">
                        <i class="bi bi-chevron-down" id="jobChevron"
                            style="color:var(--text-muted);font-size:11px;flex-shrink:0;transition:transform .2s"></i>
                    </div>

                    <div id="jobTags" style="display:flex;flex-wrap:wrap;gap:5px;margin-top:7px"></div>

                    <input type="hidden" name="job_id" id="jobIdSingle" value="{{ $expense->job_id }}">
                    <input type="hidden" name="job_ref_no" id="jobRefNo" value="{{ $expense->job_ref_no }}">

                    <div style="font-size:11px;color:var(--text-muted);margin-top:5px">
                        Currently: <strong>{{ $expense->job_ref_no ?: 'None selected' }}</strong>
                        · Multiple jobs can be selected
                    </div>
                </div>

            </div>
        </div>

        {{-- ── SECTION 2 ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">
                <div class="exp-grid3">

                    <div class="form-group">
                        <label class="form-label">Date:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;color:var(--text-muted);font-size:16px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-calendar3"></i>
                            </span>
                            <input type="datetime-local" name="expense_date" class="form-control"
                                style="border:none;border-radius:0;flex:1"
                                value="{{ $expense->expense_date ? $expense->expense_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Expense for:
                            <span class="exp-info-icon" title="Select which user this expense is for">i</span>
                        </label>
                        <select name="expense_for" class="form-select exp-select">
                            <option value="None" {{ $expense->expense_for == 'None' ? 'selected' : '' }}>None</option>
                            @foreach($users as $user)
                            <option value="{{ $user->name }}" {{ $expense->expense_for == $user->name ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Expense for contact:</label>
                        <select name="expense_for_contact" class="form-select exp-select">
                            <option value="">Please Select</option>
                            @foreach($contacts as $c)
                            <option value="{{ $c->business_name }}"
                                {{ $expense->expense_for_contact == $c->business_name ? 'selected' : '' }}>
                                {{ $c->business_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="exp-grid3" style="margin-top:20px">

                    <div class="form-group">
                        <label class="form-label">Attach Document:</label>
                        @if($expense->document_path)
                        <div style="margin-bottom:6px;font-size:12px;color:var(--success)">
                            <i class="bi bi-file-earmark-check"></i>
                            Current: <a href="/storage/{{ $expense->document_path }}" target="_blank"
                                style="color:var(--primary)">View file</a>
                        </div>
                        @endif
                        <div style="display:flex;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <input type="text" id="fileNameDisplay" placeholder="Replace document (optional)" readonly
                                class="form-control" style="border:none;border-radius:0;flex:1;background:#f8fafc;font-size:12px">
                            <label for="docFile"
                                style="display:inline-flex;align-items:center;gap:6px;padding:0 14px;
                                      background:#1a56db;color:#fff;font-size:12px;font-weight:600;
                                      cursor:pointer;white-space:nowrap">
                                <i class="bi bi-folder2-open"></i> Browse..
                            </label>
                            <input type="file" id="docFile" name="document" style="display:none"
                                accept=".pdf,.csv,.zip,.doc,.docx,.jpeg,.jpg,.png"
                                onchange="document.getElementById('fileNameDisplay').value=this.files[0]?.name||''">
                        </div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px">Max File size: 5MB · Allowed: .pdf .csv .zip .doc .docx .jpeg .jpg .png</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Applicable Tax:</label>
                        <div style="display:flex;align-items:center;gap:8px">
                            <span class="exp-info-icon" style="flex-shrink:0">i</span>
                            <select name="applicable_tax" class="form-select exp-select" style="flex:1">
                                @foreach(['None','5%','10%','15%'] as $tax)
                                <option value="{{ $tax }}" {{ $expense->applicable_tax == $tax ? 'selected' : '' }}>{{ $tax }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total amount:<span style="color:var(--danger)">*</span></label>
                        <input type="number" name="total_amount" id="totalAmount"
                            class="form-control" placeholder="Total amount"
                            step="0.01" min="0" value="{{ $expense->total_amount }}" required>
                    </div>

                </div>

                <div style="display:flex;gap:40px;margin-top:20px;flex-wrap:wrap">
                    <div class="form-group" style="flex:1;min-width:280px">
                        <label class="form-label">Expense note:</label>
                        <textarea name="expense_note" class="form-control"
                            style="min-height:110px;resize:vertical"
                            placeholder="Enter expense notes...">{{ $expense->expense_note }}</textarea>
                    </div>
                    <div class="form-group" style="padding-top:28px">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;font-weight:600">
                            <input type="checkbox" name="is_refund" value="1"
                                {{ $expense->is_refund ? 'checked' : '' }}
                                style="width:16px;height:16px;accent-color:var(--primary)">
                            Is refund?
                            <span class="exp-info-icon" title="Check if this is a refund expense">i</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SECTION 3: Recurring ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:20px 24px">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;align-items:start">
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;font-weight:600">
                            <input type="checkbox" name="is_recurring" id="isRecurring" value="1"
                                {{ $expense->is_recurring ? 'checked' : '' }}
                                style="width:16px;height:16px;accent-color:var(--primary)"
                                onchange="document.getElementById('recurringFields').style.opacity=this.checked?'1':'.4'">
                            Is Recurring?
                            <span class="exp-info-icon" title="Enable to repeat this expense automatically">i</span>
                        </label>
                    </div>
                    <div id="recurringFields"
                        style="display:contents;opacity:{{ $expense->is_recurring ? '1' : '.4' }};transition:opacity .2s">
                        <div class="form-group">
                            <label class="form-label">Recurring interval:<span style="color:var(--danger)">*</span></label>
                            <div style="display:flex;gap:8px">
                                <input type="number" name="recurring_interval" class="form-control"
                                    style="width:80px" min="1" value="{{ $expense->recurring_interval }}">
                                <select name="recurring_interval_type" class="form-select" style="flex:1">
                                    @foreach(['Days','Months','Years'] as $t)
                                    <option value="{{ $t }}" {{ $expense->recurring_interval_type == $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. of Repetitions:</label>
                            <input type="number" name="no_of_repetitions" class="form-control"
                                min="0" value="{{ $expense->no_of_repetitions }}">
                            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">If blank expense will be generated infinite times</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SECTION 4: Payment ── --}}
        <div class="card" style="margin-bottom:24px">
            <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border)">
                <span style="font-size:15px;font-weight:700;color:var(--text-primary)">Payment details</span>
            </div>
            <div class="card-body" style="padding:24px">

                {{-- Current payment status banner --}}
                @php
                $sc = ['Paid'=>'background:#d1fae5;color:#065f46','Due'=>'background:#fee2e2;color:#991b1b','Partial'=>'background:#fef3c7;color:#92400e'];
                $st = $expense->payment_status ?? 'Due';
                @endphp
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;
                        padding:10px 16px;border-radius:var(--radius-sm);{{ $sc[$st] ?? 'background:#e2e8f0;color:#475569' }}">
                    <i class="bi bi-info-circle-fill"></i>
                    Current status: <strong>{{ $st }}</strong> ·
                    Paid: <strong>TK. {{ number_format($expense->payment_amount, 2) }}</strong> ·
                    Due: <strong>TK. {{ number_format($expense->payment_due, 2) }}</strong>
                </div>

                <div class="exp-grid3" style="margin-bottom:20px">
                    <div class="form-group">
                        <label class="form-label">Amount:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-cash" style="color:var(--text-muted)"></i>
                            </span>
                            <input type="number" name="payment_amount" id="paymentAmount"
                                class="form-control" style="border:none;border-radius:0;flex:1"
                                value="{{ $expense->payment_amount }}" step="0.01" min="0">
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
                                value="{{ $expense->paid_on ? $expense->paid_on->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Payment Method:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-credit-card" style="color:var(--text-muted)"></i>
                            </span>
                            <select name="payment_method" class="form-select" style="border:none;border-radius:0;flex:1">
                                @foreach(['Cash','Bank Transfer','Cheque','bKash','Nagad'] as $m)
                                <option value="{{ $m }}" {{ $expense->payment_method == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="max-width: 400px;">
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

                <div class="form-group" style="margin-bottom:20px">
                    <label class="form-label">Payment note:</label>
                    <textarea name="payment_note" class="form-control"
                        style="min-height:90px;resize:vertical"
                        placeholder="Payment notes...">{{ $expense->payment_note }}</textarea>
                </div>

                <hr style="border:none;border-top:1px solid var(--border);margin-bottom:12px">
                <div style="text-align:right;font-size:14px;font-weight:700;color:var(--text-primary)">
                    Payment due: <span id="paymentDueDisplay" style="color:var(--danger)">
                        {{ number_format($expense->payment_due, 2) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Buttons --}}
        <div style="display:flex;justify-content:center;gap:12px;margin-bottom:32px">
            <a href="{{ route('expenses.list') }}" class="btn btn-outline" style="padding:13px 32px;font-size:15px">
                Cancel
            </a>
            <button type="submit" class="btn"
                style="background:#7c3aed;color:#fff;min-width:160px;
                       font-size:15px;font-weight:700;padding:13px 36px;
                       border-radius:8px;box-shadow:0 4px 14px rgba(124,58,237,.3)">
                <i class="bi bi-check-lg"></i> Update Expense
            </button>
        </div>

    </form>
</div>

{{-- Body-level job dropdown --}}
<div id="jobFloatingDropdown"
    style="display:none;position:fixed;background:#fff;
            border:1.5px solid var(--primary);border-radius:var(--radius-sm);
            box-shadow:0 8px 28px rgba(15,31,75,.14);
            max-height:260px;overflow:hidden;z-index:9999;flex-direction:column">

    <div style="display:flex;justify-content:space-between;align-items:center;
                padding:8px 12px;border-bottom:1px solid var(--border);
                background:var(--body-bg);flex-shrink:0">
        <span style="font-size:11px;font-weight:700;color:var(--text-muted);
                     text-transform:uppercase;letter-spacing:.06em">Select Jobs</span>
        <div style="display:flex;gap:10px">
            <button type="button" onclick="selectAllJobs()"
                style="font-size:12px;color:var(--primary);background:none;border:none;cursor:pointer;font-weight:700;padding:0">
                Select All
            </button>
            <button type="button" onclick="clearAllJobs()"
                style="font-size:12px;color:var(--danger);background:none;border:none;cursor:pointer;font-weight:700;padding:0">
                Clear
            </button>
        </div>
    </div>

    <div id="jobOptionsList" style="overflow-y:auto;flex:1">
        @foreach($jobs as $job)
        @php $ref = $job->job_no ?? $job->job_id; @endphp
        <label class="job-option" data-ref="{{ $ref }}"
            style="display:flex;align-items:center;gap:10px;padding:9px 12px;
               cursor:pointer;border-bottom:1px solid var(--border);transition:background .12s"
            onmouseover="this.style.background='var(--primary-light)'"
            onmouseout="this.style.background=''">
            <input type="checkbox"
                value="{{ $job->id }}"
                data-ref="{{ $ref }}"
                class="job-check"
                {{ $expense->jobs->contains('id', $job->id) ? 'checked' : '' }}
                style="accent-color:var(--primary);width:15px;height:15px;flex-shrink:0"
                onchange="syncTags()" onclick="event.stopPropagation()">
            <span style="font-size:13px;font-weight:600;color:var(--primary)">{{ $ref }}</span>
            @if(!empty($job->client_name))
            <span style="font-size:12px;color:var(--text-muted);margin-left:auto">{{ $job->client_name }}</span>
            @endif
        </label>
        @endforeach
    </div>

    <div id="jobNoResults"
        style="display:none;padding:20px;text-align:center;font-size:13px;color:var(--text-muted)">
        No jobs found
    </div>
</div>

@endsection

@push('styles')
<style>
    .exp-grid3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px 24px;
        align-items: start;
    }

    .exp-select {
        appearance: auto;
        -webkit-appearance: auto;
    }

    .exp-info-icon {
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
        flex-shrink: 0;
    }

    @media(max-width:900px) {
        .exp-grid3 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media(max-width:600px) {
        .exp-grid3 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // ── Subcategories — pre-load current on page load ────────────────────────────
    const currentSubCat = '{{ $expense->sub_category }}';
    const currentCat = '{{ $expense->expense_category }}';

    if (currentCat) {
        fetch(`/expenses/subcategories/ajax?parent=${encodeURIComponent(currentCat)}`)
            .then(r => r.json())
            .then(data => {
                const subCat = document.getElementById('subCat');
                subCat.innerHTML = '<option value="">Please Select</option>';
                data.forEach(c => {
                    const o = document.createElement('option');
                    o.value = c.name;
                    o.textContent = c.name;
                    if (c.name === currentSubCat) o.selected = true;
                    subCat.appendChild(o);
                });
            });
    }

    document.getElementById('expCat').addEventListener('change', function() {
        const parent = this.value;
        const subCat = document.getElementById('subCat');
        subCat.innerHTML = '<option value="">Please Select</option>';
        if (!parent) return;
        fetch(`/expenses/subcategories/ajax?parent=${encodeURIComponent(parent)}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(c => {
                    const o = document.createElement('option');
                    o.value = c.name;
                    o.textContent = c.name;
                    subCat.appendChild(o);
                });
            });
    });

    // ── Job dropdown ─────────────────────────────────────────────────────────────
    const floatDD = document.getElementById('jobFloatingDropdown');
    const jobChevron = document.getElementById('jobChevron');
    const jobTags = document.getElementById('jobTags');
    const trigger = document.getElementById('jobTrigger');
    let ddOpen = false;

    function positionDD() {
        const r = trigger.getBoundingClientRect();
        const ddW = Math.max(trigger.offsetWidth, 400);
        floatDD.style.width = ddW + 'px';
        floatDD.style.left = r.left + 'px';
        floatDD.style.top = (window.innerHeight - r.bottom < 270) ?
            (r.top - 264) + 'px' :
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
    ['scroll', 'resize'].forEach(ev => window.addEventListener(ev, () => {
        if (ddOpen) positionDD();
    }, true));

    function filterJobs(val) {
        openJobDropdown();
        const v = val.toLowerCase().trim();
        let any = false;
        document.querySelectorAll('.job-option').forEach(opt => {
            const match = !v || opt.dataset.ref.toLowerCase().includes(v);
            opt.style.display = match ? 'flex' : 'none';
            if (match) any = true;
        });
        document.getElementById('jobNoResults').style.display = any ? 'none' : 'block';
    }

    function syncTags() {
        const checked = [...document.querySelectorAll('.job-check:checked')];
        jobTags.innerHTML = '';
        const refs = [],
            ids = [];

        checked.forEach(cb => {
            refs.push(cb.dataset.ref);
            ids.push(cb.value);
            const tag = document.createElement('span');
            tag.style.cssText = 'display:inline-flex;align-items:center;gap:5px;' +
                'background:var(--primary-light);color:var(--primary);' +
                'border:1px solid var(--primary);border-radius:20px;' +
                'padding:3px 10px;font-size:12px;font-weight:600';
            tag.innerHTML = cb.dataset.ref +
                `<button type="button" onclick="removeJob('${cb.value}')"
            style="background:none;border:none;cursor:pointer;color:var(--primary);
                   font-size:15px;line-height:1;padding:0">&times;</button>`;
            jobTags.appendChild(tag);
        });

        document.getElementById('jobIdSingle').value = ids[0] || '';
        document.getElementById('jobRefNo').value = refs.join(', ');
        console.log('Selected jobs:', ids);
    }

    function removeJob(id) {
        const cb = document.querySelector(`.job-check[value="${id}"]`);
        if (cb) {
            cb.checked = false;
            syncTags();
        }
    }

    function selectAllJobs() {
        document.querySelectorAll('.job-option:not([style*="display: none"]) .job-check').forEach(cb => cb.checked = true);
        syncTags();
    }

    function clearAllJobs() {
        document.querySelectorAll('.job-check').forEach(cb => cb.checked = false);
        syncTags();
        document.getElementById('jobSearch').value = '';
        filterJobs('');
    }
    document.getElementById('expenseForm').addEventListener('submit', function(e) {
        const form = this;

        // Remove previously injected inputs
        form.querySelectorAll('input.job-hidden').forEach(el => el.remove());

        // Add job_ids_submitted marker
        const marker = document.createElement('input');
        marker.type = 'hidden';
        marker.name = 'job_ids_submitted';
        marker.value = '1';
        marker.classList.add('job-hidden');
        form.appendChild(marker);

        // Inject each selected job as hidden input
        document.querySelectorAll('.job-check:checked').forEach(cb => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'job_ids[]';
            hidden.value = cb.value;
            hidden.classList.add('job-hidden');
            form.appendChild(hidden);
        });

        console.log('Submitting job_ids:', [...document.querySelectorAll('.job-check:checked')].map(cb => cb.value));
    });

    // Pre-populate tags for already-selected job on load
    syncTags();

    // ── Payment due calc ──────────────────────────────────────────────────────────
    function calcDue() {
        const total = parseFloat(document.getElementById('totalAmount').value) || 0;
        const paid = parseFloat(document.getElementById('paymentAmount').value) || 0;
        document.getElementById('paymentDueDisplay').textContent = Math.max(0, total - paid).toFixed(2);
    }
    document.getElementById('totalAmount').addEventListener('input', calcDue);
    document.getElementById('paymentAmount').addEventListener('input', calcDue);
</script>
@endpush