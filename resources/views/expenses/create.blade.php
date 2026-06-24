@extends('layouts.app')
@section('title','Add Expense')
@section('page-title','Add Expense')
@section('breadcrumb','Expenses / Add Expense')

@section('content')
<div>
    <div style="margin-bottom:20px">
        <h2 style="font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            Add Expense
        </h2>
    </div>

    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" id="expenseForm">
        @csrf

        {{-- ── SECTION 1 ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">
                <div class="exp-grid3" style="margin-bottom:20px">

                    <div class="form-group">
                        <label class="form-label">Expense Category:</label>
                        <select name="expense_category" id="expCat" class="form-select exp-select">
                            <option value="">Please Select</option>
                            <option value="Job Expense">Job Expense</option>
                            <option value="Office Expense">Office Expense</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Sub category:</label>
                        <select name="sub_category" id="subCat" class="form-select exp-select">
                            <option value="">Please Select</option>
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

                    <!-- <input type="hidden" name="job_id" id="jobIdSingle">
                    <input type="hidden" name="job_no" id="jobfNo"> -->

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
                                value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>

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

                    <div class="form-group">
                        <label class="form-label">Attach Document:</label>
                        <div style="display:flex;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <input type="text" id="fileNameDisplay" placeholder="" readonly
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
                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px">Max File size: 5MB</div>
                        <div style="font-size:11px;color:var(--text-muted)">Allowed: .pdf .csv .zip .doc .docx .jpeg .jpg .png</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Total amount:<span style="color:var(--danger)">*</span></label>
                        <input type="number" name="total_amount" id="totalAmount"
                            class="form-control" placeholder="Total amount"
                            step="0.01" min="0" value="{{ old('total_amount') }}" required>
                    </div>

                </div>

                <div style="display:flex;gap:40px;margin-top:20px;flex-wrap:wrap">
                    <div class="form-group" style="flex:1;min-width:280px">
                        <label class="form-label">Expense note:</label>
                        <textarea name="expense_note" class="form-control"
                            style="min-height:110px;resize:vertical"
                            placeholder="Enter expense notes...">{{ old('expense_note') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SECTION 3: Add Payment ── --}}
        <div class="card" style="margin-bottom:24px">
            <div style="padding:18px 24px 14px;border-bottom:1px solid var(--border)">
                <span style="font-size:15px;font-weight:700;color:var(--text-primary)">Add payment</span>
            </div>
            <div class="card-body" style="padding:24px">
                <div class="exp-grid3" style="margin-bottom:20px">

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

                <div class="form-group" style="margin-bottom:20px">
                    <label class="form-label">Payment note:</label>
                    <textarea name="payment_note" class="form-control"
                        style="min-height:90px;resize:vertical"
                        placeholder="Payment notes..."></textarea>
                </div>

                <hr style="border:none;border-top:1px solid var(--border);margin-bottom:12px">
                <div style="text-align:right;font-size:14px;font-weight:700;color:var(--text-primary)">
                    Total Amount: <span style="color:var(--primary)" id="totalAmountDisplay">0.00</span>
                </div>
            </div>
        </div>

        {{-- Hidden checkbox pool to track selections --}}
        <div id="jobCheckboxPool" style="display:none">
            @foreach(\App\Models\Job::orderBy('id', 'desc')->get() as $job)
            <input type="checkbox"
                class="job-check"
                name="job_ids[]"
                value="{{ $job->id }}" {{-- ✅ Use the integer ID --}}
                data-ref="{{ $job->job_no ?? $job->job_id }}"
                data-client="{{ $job->client_name }}"
                data-category="{{ $job->category }}"
                data-invoice="{{ $job->invoice_value_usd }}"
                data-imp-exp="{{ $job->imp_exp_value }}">
            @endforeach
        </div>

        {{-- Save --}}
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
            data-search="{{ strtolower(($job->job_id ?? $job->job_no) . ' ' . ($job->client_name ?? '') . ' ' . ($job->category ?? '') . ' ' . ($job->type ?? '')) }}"
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
                {{-- Top row: Job Number + Category + Type Badges --}}
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap">
                    <span style="font-size:13px;font-weight:700;color:var(--primary);
                                 font-family:'Inter',sans-serif">
                        {{ $job->job_no ?? $job->job_id }}
                    </span>

                    {{-- Category Badge --}}
                    @if($job->category)
                    <span style="font-size:10px;padding:2px 8px;border-radius:10px;
                                 background:{{ str_contains(strtolower($job->category), 'import') ? '#dbeafe' : '#fef3c7' }};
                                 color:{{ str_contains(strtolower($job->category), 'import') ? '#1e40af' : '#92400e' }};
                                 font-weight:600;text-transform:uppercase;letter-spacing:0.3px">
                        {{ $job->category }}
                    </span>
                    @endif

                    {{-- Type Badge --}}
                    @if($job->type)
                    <span style="font-size:10px;padding:2px 8px;border-radius:10px;
                                 background:#e0e7ff;color:#3730a3;
                                 font-weight:600;text-transform:uppercase;letter-spacing:0.3px">
                        {{ $job->type }}
                    </span>
                    @endif
                </div>

                {{-- Bottom row: Client Name --}}
                <div style="font-size:12px;color:var(--text-muted);
                            overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
                            display:flex;align-items:center;gap:6px">
                    <i class="bi bi-person-fill" style="color:var(--primary);font-size:13px"></i>
                    <span style="font-weight:500;color:var(--text-primary)">
                        {{ $job->client_name ?? 'No client' }}
                    </span>
                </div>
            </div>

            {{-- Right side: Invoice Value --}}
            @if($job->invoice_value_usd)
            <div style="text-align:right;flex-shrink:0;border-left:1px solid var(--border);padding-left:12px">
                <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600">
                    Invoice
                </div>
                <div style="font-size:13px;font-weight:700;color:var(--success, #059669);font-family:'Inter',sans-serif">
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
        <i class="bi bi-search" style="font-size:24px;display:block;margin-bottom:8px;opacity:0.5"></i>
        No jobs found matching your search.
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Inter font */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    body,
    input,
    select,
    textarea,
    button {
        font-family: 'Inter', sans-serif !important;
    }

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
    // ── Subcategories + disable job/sub when Office Expense ──────────────────────
    document.getElementById('expCat').addEventListener('change', function() {
        const parent = this.value;
        const subCat = document.getElementById('subCat');
        const jobWrap = document.getElementById('jobTrigger');
        const jobSearch = document.getElementById('jobSearch');
        const isOffice = parent === 'Office Expense';

        console.log('Category changed to:', parent);

        // Subcategory is ALWAYS enabled - never disabled
        subCat.innerHTML = '<option value="">Please Select</option>';
        subCat.disabled = false;
        subCat.style.opacity = '1';
        subCat.style.cursor = '';
        subCat.style.background = '';

        // Only disable JOB NUMBER for Office Expense
        jobWrap.style.opacity = isOffice ? '.45' : '1';
        jobWrap.style.pointerEvents = isOffice ? 'none' : '';
        jobWrap.style.cursor = isOffice ? 'not-allowed' : 'pointer';
        jobWrap.style.background = isOffice ? '#f1f5f9' : '#fff';
        jobSearch.disabled = isOffice;

        if (isOffice) {
            closeJobDropdown();
            clearAllJobs();
        }

        // Return only if no category selected
        if (!parent) return;

        //  Fetch subcategories for BOTH Job Expense AND Office Expense
        fetch(`/expenses/subcategories/ajax?parent=${encodeURIComponent(parent)}`)
            .then(r => r.json())
            .then(data => {
                console.log('Subcategories loaded for', parent, ':', data);

                data.forEach(c => {
                    const o = document.createElement('option');
                    o.value = c.name;
                    o.textContent = c.name;
                    subCat.appendChild(o);
                });
            })
            .catch(error => {
                console.error('Error fetching subcategories:', error);
            });
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
            const searchData = (opt.dataset.search || '').toLowerCase();
            const match = !v || searchData.includes(v);
            opt.style.display = match ? 'flex' : 'none';
            if (match) any = true;
        });
        document.getElementById('jobNoResults').style.display = any ? 'none' : 'block';
    }

    // ✅ Sync tags, hidden inputs, and count badge
    function syncTags() {
        const checked = [...document.querySelectorAll('#jobCheckboxPool .job-check:checked')];
        jobTags.innerHTML = '';
        const refs = [];
        const ids = [];

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

        // ✅ Update hidden inputs (safely check if they exist)
        const jobIdSingle = document.getElementById('jobIdSingle');
        const jobNoInput = document.getElementById('jobfNo');

        if (jobIdSingle) jobIdSingle.value = ids[0] || '';
        if (jobNoInput) jobNoInput.value = refs.join(', ');

        // ✅ Update count badge
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

    // ✅ CRITICAL FIX: Before form submit, ensure checkboxes are in the form
    document.getElementById('expenseForm').addEventListener('submit', function(e) {
        // Move checked checkboxes into the form so they get submitted
        const form = this;
        const checkedJobs = document.querySelectorAll('#jobCheckboxPool .job-check:checked');

        // Remove any previously appended hidden inputs from the form
        form.querySelectorAll('input[name="job_ids[]"]').forEach(inp => inp.remove());

        // Add hidden inputs for each selected job
        checkedJobs.forEach(cb => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'job_ids[]';
            hidden.value = cb.value;
            form.appendChild(hidden);
        });

        console.log('Submitting with job_ids:', [...checkedJobs].map(cb => cb.value));
    });

    // ── Total amount display ──────────────────────────────────────────────────────
    document.getElementById('totalAmount').addEventListener('input', function() {
        const v = parseFloat(this.value) || 0;
        document.getElementById('totalAmountDisplay').textContent = v.toFixed(2);
    });
</script>
@endpush