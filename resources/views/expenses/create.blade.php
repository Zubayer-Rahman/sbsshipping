@extends('layouts.app')
@section('title','Add Expense')
@section('page-title','Add Expense')
@section('breadcrumb','Expenses / Add Expense')

@section('content')
<div>
    <div style="margin-bottom:20px">
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            Add Expense
        </h2>
    </div>

    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" id="expenseForm">
        @csrf

        {{-- ── SECTION 1 ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">
                <div class="exp-grid3" style="margin-bottom:20px">

                    {{-- Expense Category --}}
                    <div class="form-group">
                        <label class="form-label">Expense Category:</label>
                        <select name="expense_category" id="expCat" class="form-select exp-select">
                            <option value="">Please Select</option>
                            <option value="Job Expense">Job Expense</option>
                            <option value="Office Expense">Office Expense</option>
                        </select>
                    </div>

                    {{-- Sub Category --}}
                    <div class="form-group">
                        <label class="form-label">Sub category:</label>
                        <select name="sub_category" id="subCat" class="form-select exp-select">
                            <option value="">Please Select</option>
                        </select>
                    </div>

                    {{-- JOB Number --}}
                    <div class="form-group" style="margin-bottom:6px">
                        <label class="form-label" style="font-weight:700">
                            JOB Number<span style="color:var(--danger)">*</span> <span style="font-weight:400;color:var(--text-muted)">(No Need To Select Job No. If Office Expenses)</span>
                        </label>
                        <div style="max-width:480px;position:relative">

                            {{-- Search input that opens the dropdown --}}
                            <div id="jobDropdownTrigger"
                                style="display:flex;align-items:center;border:1.5px solid var(--border);
                    border-radius:var(--radius-sm);cursor:pointer;background:#fff;
                    padding:0 10px;height:40px;gap:8px;user-select:none"
                                onclick="toggleJobDropdown()">
                                <i class="bi bi-search" style="color:var(--text-muted);font-size:13px;flex-shrink:0"></i>
                                <input type="text" id="jobSearch" placeholder="Search or select job numbers..."
                                    onclick="event.stopPropagation();showJobDropdown()"
                                    oninput="filterJobs(this.value)"
                                    style="border:none;outline:none;flex:1;font-size:13px;
                          font-family:inherit;background:transparent;cursor:text">
                                <i class="bi bi-chevron-down" id="jobChevron"
                                    style="color:var(--text-muted);font-size:11px;flex-shrink:0;transition:transform .2s"></i>
                            </div>

                            {{-- Selected tags display --}}
                            <div id="jobTags" style="display:flex;flex-wrap:wrap;gap:5px;margin-top:6px;min-height:0"></div>

                            {{-- Dropdown list --}}
                            <div id="jobDropdown"
                                style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;
                    background:#fff;border:1.5px solid var(--primary);border-radius:var(--radius-sm);
                    max-height:220px;overflow-y:auto;z-index:500;
                    box-shadow:0 8px 24px rgba(15,31,75,.12)">

                                {{-- Select All / Clear --}}
                                <div style="display:flex;justify-content:space-between;align-items:center;
                        padding:8px 12px;border-bottom:1px solid var(--border);
                        background:var(--body-bg);position:sticky;top:0;z-index:1">
                                    <span style="font-size:11px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em">
                                        Select Jobs
                                    </span>
                                    <div style="display:flex;gap:10px">
                                        <button type="button" onclick="selectAllJobs()"
                                            style="font-size:12px;color:var(--primary);background:none;
                                   border:none;cursor:pointer;font-weight:600;padding:0">
                                            Select All
                                        </button>
                                        <button type="button" onclick="clearAllJobs()"
                                            style="font-size:12px;color:var(--danger);background:none;
                                   border:none;cursor:pointer;font-weight:600;padding:0">
                                            Clear
                                        </button>
                                    </div>
                                </div>

                                {{-- Job options --}}
                                <div id="jobOptions">
                                    @foreach($jobs as $job)
                                    @php $ref = $job->job_no ?? $job->job_id; @endphp
                                    <label class="job-option"
                                        data-ref="{{ $ref }}"
                                        style="display:flex;align-items:center;gap:10px;
                              padding:9px 12px;cursor:pointer;transition:background .12s;
                              border-bottom:1px solid var(--border)">
                                        <input type="checkbox" name="job_ids[]"
                                            value="{{ $job->id }}"
                                            data-ref="{{ $ref }}"
                                            class="job-check"
                                            style="accent-color:var(--primary);width:15px;height:15px;flex-shrink:0"
                                            onchange="syncTags()"
                                            required>
                                        <span style="font-size:13px;font-weight:600;color:var(--primary)">{{ $ref }}</span>
                                        @if(!empty($job->client_name))
                                        <span style="font-size:12px;color:var(--text-muted);margin-left:auto">
                                            {{ Str::limit($job->client_name, 28) }}
                                        </span>
                                        @endif
                                    </label>
                                    @endforeach
                                </div>

                                <div id="jobNoResults"
                                    style="display:none;padding:20px;text-align:center;
                        font-size:13px;color:var(--text-muted)">
                                    No jobs found
                                </div>
                            </div>

                            {{-- Hidden single job_id for backward compat (first selected) --}}
                            <input type="hidden" name="job_id" id="jobIdSingle">
                            <input type="hidden" name="job_ref_no" id="jobRefNo">

                            <div style="font-size:11px;color:var(--text-muted);margin-top:6px">
                                Leave empty to autogenerate · Multiple jobs can be selected
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group" style="margin-bottom:6px">
                        <label class="form-label" style="font-weight:700">
                            JOB Number:<span style="color:var(--danger)">*</span>
                        </label>
                        <div style="max-width:420px">
                            <select name="job_id" id="jobSelect" class="form-select exp-select" required>
                                <option value="">Select Job Number</option>
                                @foreach($jobs as $job)
                                    <option value="{{ $job->id }}"
                                        data-ref="{{ $job->job_no ?? $job->job_id }}">
                                        {{ $job->job_no ?? $job->job_id }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="job_ref_no" id="jobRefNo">
                            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">Leave empty to autogenerate</div>
                        </div>
                    </div> -->
                </div>


            </div>
        </div>

        {{-- ── SECTION 2 ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">
                <div class="exp-grid3">

                    {{-- Date --}}
                    <div class="form-group">
                        <label class="form-label">Date:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;color:var(--text-muted);font-size:16px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-calendar3"></i>
                            </span>
                            <input type="datetime-local" name="expense_date" class="form-control"
                                style="border:none;border-radius:0;flex:1" required>
                        </div>
                    </div>

                    {{-- Expense For --}}
                    <div class="form-group">
                        <label class="form-label">
                            Expense for:
                            <span class="exp-info-icon" title="Select which user this expense is for">i</span>
                        </label>
                        <select name="expense_for" class="form-select exp-select">
                            <option value="None">None</option>
                            @foreach($users as $user)
                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Expense for contact --}}
                    <div class="form-group">
                        <label class="form-label">Expense for contact:</label>
                        <select name="expense_for_contact" class="form-select exp-select">
                            <option value="">Please Select</option>
                            @foreach($contacts as $c)
                            <option value="{{ $c->business_name }}">{{ $c->business_name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="exp-grid3" style="margin-top:20px">

                    {{-- Attach Document --}}
                    <div class="form-group">
                        <label class="form-label">Attach Document:</label>
                        <div style="display:flex;gap:0;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <input type="text" id="fileNameDisplay" placeholder="" readonly
                                class="form-control" style="border:none;border-radius:0;flex:1;background:#f8fafc;font-size:12px">
                            <label for="docFile"
                                style="display:inline-flex;align-items:center;gap:6px;
                                      padding:0 14px;background:#1a56db;color:#fff;
                                      font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap">
                                <i class="bi bi-folder2-open"></i> Browse..
                            </label>
                            <input type="file" id="docFile" name="document" style="display:none"
                                accept=".pdf,.csv,.zip,.doc,.docx,.jpeg,.jpg,.png"
                                onchange="document.getElementById('fileNameDisplay').value=this.files[0]?.name||''">
                        </div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px">Max File size: 5MB</div>
                        <div style="font-size:11px;color:var(--text-muted)">Allowed File: .pdf, .csv, .zip, .doc, .docx, .jpeg, .jpg, .png</div>
                    </div>

                    {{-- Applicable Tax --}}
                    <!-- <div class="form-group">
                        <label class="form-label">Applicable Tax:</label>
                        <div style="display:flex;align-items:center;gap:8px">
                            <span class="exp-info-icon" style="flex-shrink:0">i</span>
                            <select name="applicable_tax" class="form-select exp-select" style="flex:1">
                                <option value="None">None</option>
                                <option value="5%">5%</option>
                                <option value="10%">10%</option>
                                <option value="15%">15%</option>
                            </select>
                        </div>
                    </div> -->

                    {{-- Total Amount --}}
                    <div class="form-group">
                        <label class="form-label">Total amount:<span style="color:var(--danger)">*</span></label>
                        <input type="number" name="total_amount" id="totalAmount"
                            class="form-control" placeholder="Total amount"
                            step="0.01" min="0" value="{{ old('total_amount') }}" required>
                    </div>

                </div>

                <div style="display:flex;gap:40px;margin-top:20px;flex-wrap:wrap">

                    {{-- Expense Note --}}
                    <div class="form-group" style="flex:1;min-width:280px">
                        <label class="form-label">Expense note:</label>
                        <textarea name="expense_note" class="form-control"
                            style="min-height:110px;resize:vertical"
                            placeholder="Enter expense notes...">{{ old('expense_note') }}</textarea>
                    </div>

                    {{-- Is Refund --}}
                    <!-- <div class="form-group" style="padding-top:28px">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;font-weight:600">
                            <input type="checkbox" name="is_refund" value="1"
                                style="width:16px;height:16px;accent-color:var(--primary)">
                            Is refund?
                            <span class="exp-info-icon" title="Check if this is a refund expense">i</span>
                        </label>
                    </div> -->

                </div>
            </div>
        </div>

        <!-- {{-- ── SECTION 3: Recurring ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:20px 24px">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;align-items:start">

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;font-weight:600">
                            <input type="checkbox" name="is_recurring" id="isRecurring" value="1"
                                style="width:16px;height:16px;accent-color:var(--primary)"
                                onchange="document.getElementById('recurringFields').style.opacity=this.checked?'1':'.4'">
                            Is Recurring?
                            <span class="exp-info-icon" title="Enable to repeat this expense automatically">i</span>
                        </label>
                    </div>

                    <div id="recurringFields" style="display:contents;opacity:.4;transition:opacity .2s">
                        <div class="form-group">
                            <label class="form-label">Recurring interval:<span style="color:var(--danger)">*</span></label>
                            <div style="display:flex;gap:8px">
                                <input type="number" name="recurring_interval" class="form-control"
                                    style="width:80px" min="1" placeholder="">
                                <select name="recurring_interval_type" class="form-select" style="flex:1">
                                    <option value="Days">Days</option>
                                    <option value="Months">Months</option>
                                    <option value="Years">Years</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. of Repetitions:</label>
                            <input type="number" name="no_of_repetitions" class="form-control"
                                placeholder="" min="0">
                            <div style="font-size:11px;color:var(--text-muted);margin-top:4px">
                                If blank expense will be generated infinite times
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div> -->

        {{-- ── SECTION 4: Add Payment ── --}}
        <div class="card" style="margin-bottom:24px">
            <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border)">
                <span style="font-size:15px;font-weight:700;color:var(--text-primary)">Add payment</span>
            </div>
            <div class="card-body" style="padding:24px">

                <div class="exp-grid3" style="margin-bottom:20px">

                    {{-- Amount --}}
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

                    {{-- Paid On --}}
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

                    {{-- Payment Method --}}
                    <div class="form-group">
                        <label class="form-label">Payment Method:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-credit-card" style="color:var(--text-muted)"></i>
                            </span>
                            <select name="payment_method" class="form-select"
                                style="border:none;border-radius:0;flex:1">
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="bKash">bKash</option>
                                <option value="Nagad">Nagad</option>
                            </select>
                        </div>
                    </div>

                </div>

                {{-- Payment Account --}}
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

                {{-- Payment Note --}}
                <div class="form-group" style="margin-bottom:20px">
                    <label class="form-label">Payment note:</label>
                    <textarea name="payment_note" class="form-control"
                        style="min-height:90px;resize:vertical"
                        placeholder="Payment notes..."></textarea>
                </div>

                {{-- Divider + Payment Due --}}
                <hr style="border:none;border-top:1px solid var(--border);margin-bottom:12px">
                <div style="text-align:right;font-size:14px;font-weight:700;color:var(--text-primary)">
                    Payment due: <span id="paymentDueDisplay" style="color:var(--danger)">0.00</span>
                </div>

            </div>
        </div>

        {{-- Save Button --}}
        <div style="display:flex;justify-content:center;margin-bottom:32px">
            <button type="submit" class="btn"
                style="background:#7c3aed;color:#fff;min-width:160px;
                       font-size:15px;font-weight:700;padding:13px 36px;
                       border-radius:8px;box-shadow:0 4px 14px rgba(124,58,237,.3)">
                Save
            </button>
        </div>

    </form>
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
    // Load subcategories when expense category changes
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

    // // Store job ref when job selected
    // document.getElementById('jobSelect').addEventListener('change', function() {
    //     const opt = this.options[this.selectedIndex];
    //     document.getElementById('jobRefNo').value = opt.dataset.ref || '';
    // });

    // ── Job multi-select dropdown ────────────────────────────────────────────────
    const jobDropdown = document.getElementById('jobDropdown');
    const jobChevron = document.getElementById('jobChevron');
    const jobTags = document.getElementById('jobTags');
    let dropdownOpen = false;

    function showJobDropdown() {
        jobDropdown.style.display = 'block';
        jobChevron.style.transform = 'rotate(180deg)';
        dropdownOpen = true;
    }

    function hideJobDropdown() {
        jobDropdown.style.display = 'none';
        jobChevron.style.transform = 'rotate(0deg)';
        dropdownOpen = false;
    }

    function toggleJobDropdown() {
        dropdownOpen ? hideJobDropdown() : showJobDropdown();
    }

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#jobDropdownTrigger') &&
            !e.target.closest('#jobDropdown')) {
            hideJobDropdown();
        }
    });

    // Filter by search
    function filterJobs(val) {
        showJobDropdown();
        const v = val.toLowerCase().trim();
        let anyVisible = false;
        document.querySelectorAll('.job-option').forEach(opt => {
            const ref = opt.dataset.ref.toLowerCase();
            const show = !v || ref.includes(v);
            opt.style.display = show ? 'flex' : 'none';
            if (show) anyVisible = true;
        });
        document.getElementById('jobNoResults').style.display = anyVisible ? 'none' : 'block';
    }

    // Sync selected tags + hidden inputs
    function syncTags() {
        const checked = document.querySelectorAll('.job-check:checked');
        jobTags.innerHTML = '';

        const refs = [],
            ids = [];
        checked.forEach(cb => {
            refs.push(cb.dataset.ref);
            ids.push(cb.value);

            const tag = document.createElement('span');
            tag.style.cssText = `
            display:inline-flex;align-items:center;gap:5px;
            background:var(--primary-light);color:var(--primary);
            border:1px solid var(--primary);border-radius:20px;
            padding:3px 10px;font-size:12px;font-weight:600
        `;
            tag.innerHTML = `${cb.dataset.ref}
            <button type="button" onclick="removeJob('${cb.value}')"
                    style="background:none;border:none;cursor:pointer;
                           color:var(--primary);font-size:14px;line-height:1;padding:0">
                &times;
            </button>`;
            jobTags.appendChild(tag);
        });

        // Sync hidden fields (first selected for backward compat)
        document.getElementById('jobIdSingle').value = ids[0] || '';
        document.getElementById('jobRefNo').value = refs.join(', ');
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

    // Live payment due calculation
    function calcDue() {
        const total = parseFloat(document.getElementById('totalAmount').value) || 0;
        const paid = parseFloat(document.getElementById('paymentAmount').value) || 0;
        const due = Math.max(0, total - paid);
        document.getElementById('paymentDueDisplay').textContent = due.toFixed(2);
    }
    document.getElementById('totalAmount').addEventListener('input', calcDue);
    document.getElementById('paymentAmount').addEventListener('input', calcDue);
</script>
@endpush