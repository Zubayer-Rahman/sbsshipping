@extends('layouts.app')
@section('title', 'Add Additional Expense')
@section('content')

<div style="padding:2rem;max-width:800px;margin:0 auto;font-family:'Inter',sans-serif">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <h1 style="font-size:1.75rem;font-weight:800;color:var(--text-primary);margin:0">
            New Additional Expense
        </h1>
        <a href="{{ route('additional-expenses.index') }}"
            style="color:var(--primary);text-decoration:none;font-weight:600">← Back to List</a>
    </div>

    <div style="background:#fff;padding:32px;border-radius:12px;box-shadow:var(--shadow-md)">
        <form action="{{ route('additional-expenses.store') }}" method="POST">
            @csrf

            {{-- Reference --}}
            <div style="margin-bottom:20px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                    Reference Number
                </label>
                <input type="text" value="{{ $referenceNo }}" disabled
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;
                              background:var(--body-bg);font-size:14px">
            </div>

            {{-- Client + Job Row --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                        Client <span style="color:var(--danger)">*</span>
                    </label>
                    <select name="client_id" required
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->business_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <!-- job drop down -->
                <div class="form-group">
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                        Job <span style="font-weight:400;color:var(--text-muted);font-size:12px">(Optional - Multiple Allowed)</span>
                    </label>

                    {{-- Trigger Input --}}
                    <div id="addExpJobTrigger"
                        style="display:flex;align-items:center;gap:8px;
               border:1.5px solid var(--border);border-radius:6px;
               background:#fff;padding:0 12px;height:40px;cursor:pointer;
               transition:border-color .2s"
                        onclick="toggleAddExpJobDropdown(event)">
                        <i class="bi bi-search" style="color:var(--text-muted);font-size:13px;flex-shrink:0"></i>
                        <input type="text" id="addExpJobSearch"
                            placeholder="Search or select job numbers..."
                            autocomplete="off"
                            style="border:none;outline:none;flex:1;font-size:13px;
                   font-family:'Inter',sans-serif;background:transparent;
                   cursor:text;color:var(--text-primary)"
                            onclick="event.stopPropagation();openAddExpJobDropdown()"
                            oninput="filterAddExpJobs(this.value)">
                        <i class="bi bi-chevron-down" id="addExpJobChevron"
                            style="color:var(--text-muted);font-size:11px;flex-shrink:0;transition:transform .2s"></i>
                    </div>

                    {{-- Selected Tags --}}
                    <div id="addExpJobTags" style="display:flex;flex-wrap:wrap;gap:5px;margin-top:7px"></div>

                    {{-- Count Badge --}}
                    <div style="font-size:12px;color:var(--text-muted);margin-top:5px;
                display:flex;align-items:center;gap:10px">
                        <span id="addExpJobSelectedCount"
                            style="display:none;background:var(--primary);color:#fff;
                   font-size:11px;font-weight:700;padding:2px 10px;
                   border-radius:20px;white-space:nowrap">
                        </span>
                    </div>

                    {{-- Hidden checkbox pool --}}
                    <div id="addExpJobCheckboxPool" style="display:none">
                        @foreach($jobs as $job)
                        <input type="checkbox"
                            class="addexp-job-check"
                            name="job_ids[]"
                            value="{{ $job->id }}"
                            data-ref="{{ $job->job_no ?? $job->job_id }}"
                            data-client="{{ $job->client_name }}"
                            data-category="{{ $job->category }}"
                            data-type="{{ $job->type }}"
                            data-invoice="{{ $job->invoice_value_usd }}">
                        @endforeach
                    </div>
                </div>

                {{-- Floating Dropdown Panel for Additional Expense --}}
                <div id="addExpJobFloatingDropdown"
                    style="display:none;position:fixed;background:#fff;
           border:1.5px solid var(--primary);border-radius:6px;
           box-shadow:0 8px 28px rgba(15,31,75,.14);
           max-height:380px;z-index:9999;flex-direction:column;overflow:hidden">

                    <div style="display:flex;justify-content:space-between;align-items:center;
                padding:10px 14px;border-bottom:1px solid var(--border);
                background:var(--body-bg);flex-shrink:0">
                        <span style="font-size:12px;font-weight:700;color:var(--text-primary)">
                            Select Jobs (Multiple Allowed)
                        </span>
                        <button type="button" onclick="clearAllAddExpJobs()"
                            style="font-size:12px;color:var(--danger);background:none;
                   border:none;cursor:pointer;font-weight:600;padding:0">
                            Clear All
                        </button>
                    </div>

                    {{-- Inside the floating dropdown - visual list --}}
                    <div id="addExpJobVisualList" style="overflow-y:auto;flex:1;padding:4px">
                        @foreach($jobs as $job)
                        <div class="addexp-job-visual-option"
                            data-id="{{ $job->id }}"
                            data-search="{{ strtolower(($job->job_no ?? $job->job_id ?? '') . ' ' . ($job->client_name ?? '') . ' ' . ($job->category ?? '') . ' ' . ($job->type ?? '')) }}"
                            style="display:flex;align-items:center;gap:12px;padding:12px 14px;
               cursor:pointer;border-bottom:1px solid var(--border);
               transition:background .15s">

                            {{-- Checkbox visual --}}
                            <span class="addexp-job-visual-check"
                                style="width:18px;height:18px;border:2px solid var(--border);
                   border-radius:4px;flex-shrink:0;display:flex;
                   align-items:center;justify-content:center;
                   transition:all .2s;background:#fff">
                            </span>

                            {{-- Job info --}}
                            <div style="flex:1;min-width:0">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap">
                                    {{-- ✅ Show job_no first, fallback to job_id --}}
                                    <span style="font-size:13px;font-weight:700;color:var(--primary);
                             font-family:'Inter',sans-serif">
                                        {{ $job->job_no ?? $job->job_id ?? 'Job #'.$job->id }}
                                    </span>

                                    @if($job->category)
                                    <span style="font-size:10px;padding:2px 8px;border-radius:10px;
                             background:{{ str_contains(strtolower($job->category), 'import') ? '#dbeafe' : '#fef3c7' }};
                             color:{{ str_contains(strtolower($job->category), 'import') ? '#1e40af' : '#92400e' }};
                             font-weight:600;text-transform:uppercase;letter-spacing:0.3px">
                                        {{ $job->category }}
                                    </span>
                                    @endif

                                    @if($job->type)
                                    <span style="font-size:10px;padding:2px 8px;border-radius:10px;
                             background:#e0e7ff;color:#3730a3;
                             font-weight:600;text-transform:uppercase;letter-spacing:0.3px">
                                        {{ $job->type }}
                                    </span>
                                    @endif
                                </div>

                                <div style="font-size:12px;color:var(--text-muted);
                        overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
                        display:flex;align-items:center;gap:6px">
                                    <i class="bi bi-person-fill" style="color:var(--primary);font-size:13px"></i>
                                    <span style="font-weight:500;color:var(--text-primary)">
                                        {{ $job->client_name ?? 'No client' }}
                                    </span>
                                </div>
                            </div>

                            @if($job->invoice_value_usd)
                            <div style="text-align:right;flex-shrink:0;border-left:1px solid var(--border);padding-left:12px">
                                <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600">
                                    Invoice
                                </div>
                                <div style="font-size:13px;font-weight:700;color:#059669;font-family:'Inter',sans-serif">
                                    ${{ number_format($job->invoice_value_usd, 0) }}
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <div id="addExpJobNoResults"
                        style="display:none;padding:20px;text-align:center;font-size:13px;
               color:var(--text-muted)">
                        <i class="bi bi-search" style="font-size:24px;display:block;margin-bottom:8px;opacity:0.5"></i>
                        No jobs found matching your search.
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div style="margin-bottom:20px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                    Description <span style="color:var(--danger)">*</span>
                </label>
                <input type="text" name="description" value="{{ old('description') }}" required
                    placeholder="e.g., Extra customs clearance fee, Transportation cost"
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">


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

            {{-- Amounts Row --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                        Actual Amount <span style="color:var(--danger)">*</span>
                    </label>
                    <input type="number" name="actual_amount" value="{{ old('actual_amount', 0) }}" step="0.01" min="0" required
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
                    <small style="color:var(--text-muted);font-size:11px">What you actually paid</small>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                        To Be Billed <span style="color:var(--danger)">*</span>
                    </label>
                    <input type="number" name="to_be_billed" value="{{ old('to_be_billed', 0) }}" step="0.01" min="0" required
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
                    <small style="color:var(--text-muted);font-size:11px">What you'll charge the client</small>
                </div>
            </div>

            {{-- Date --}}
            <div style="margin-bottom:20px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                    Expense Date <span style="color:var(--danger)">*</span>
                </label>
                <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
            </div>

            {{-- Notes --}}
            <div style="margin-bottom:24px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                    Notes
                </label>
                <textarea name="notes" rows="3"
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px;resize:vertical">{{ old('notes') }}</textarea>
            </div>

            {{-- Submit --}}
            <div style="display:flex;gap:12px">
                <button type="submit"
                    style="flex:1;padding:12px;background:var(--primary);color:#fff;border:none;
                               border-radius:6px;font-weight:600;cursor:pointer;font-size:14px">
                    Save Additional Expense
                </button>
                <a href="{{ route('additional-expenses.index') }}"
                    style="padding:12px 24px;background:#64748b;color:#fff;border-radius:6px;
                          text-decoration:none;font-weight:600;font-size:14px">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection


@push('styles')
<style>
    /* Additional Expense Job Dropdown Styles */
    .addexp-job-visual-option {
        background: #fff;
    }

    .addexp-job-visual-option:hover {
        background: #f8fafc;
    }

    .addexp-job-visual-option.selected {
        background: #eff6ff;
        border-left: 3px solid var(--primary);
    }

    .addexp-job-visual-option.selected .addexp-job-visual-check {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
    }

    .addexp-job-visual-option.selected .addexp-job-visual-check::after {
        content: '✓';
        color: white;
        font-size: 12px;
        font-weight: bold;
    }

    #addExpJobVisualList::-webkit-scrollbar {
        width: 6px;
    }

    #addExpJobVisualList::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    #addExpJobVisualList::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
</style>
@endpush

@push('scripts')
<script>
    // ═══════════════════════════════════════════════════════════════
    // Additional Expense - Job Dropdown
    // ═══════════════════════════════════════════════════════════════

    const addExpFloatDD = document.getElementById('addExpJobFloatingDropdown');
    const addExpTrigger = document.getElementById('addExpJobTrigger');
    const addExpChevron = document.getElementById('addExpJobChevron');
    const addExpJobTags = document.getElementById('addExpJobTags');
    const addExpBadge = document.getElementById('addExpJobSelectedCount');
    let addExpDdOpen = false;

    function positionAddExpDD() {
        const r = addExpTrigger.getBoundingClientRect();
        const w = Math.max(addExpTrigger.offsetWidth, 420);
        addExpFloatDD.style.width = w + 'px';
        addExpFloatDD.style.left = r.left + 'px';
        const below = window.innerHeight - r.bottom;
        addExpFloatDD.style.top = below < 290 ?
            (r.top - 284) + 'px' :
            (r.bottom + 4) + 'px';
    }

    function openAddExpJobDropdown() {
        addExpFloatDD.style.display = 'flex';
        addExpDdOpen = true;
        addExpChevron.style.transform = 'rotate(180deg)';
        positionAddExpDD();
    }

    function closeAddExpJobDropdown() {
        addExpFloatDD.style.display = 'none';
        addExpDdOpen = false;
        addExpChevron.style.transform = 'rotate(0deg)';
    }

    function toggleAddExpJobDropdown(e) {
        if (e.target === document.getElementById('addExpJobSearch')) return;
        addExpDdOpen ? closeAddExpJobDropdown() : openAddExpJobDropdown();
    }

    document.addEventListener('click', e => {
        if (!addExpTrigger.contains(e.target) && !addExpFloatDD.contains(e.target)) {
            closeAddExpJobDropdown();
        }
    });

    ['scroll', 'resize'].forEach(ev =>
        window.addEventListener(ev, () => {
            if (addExpDdOpen) positionAddExpDD();
        }, true)
    );

    // Click on visual row
    document.querySelectorAll('.addexp-job-visual-option').forEach(opt => {
        opt.addEventListener('click', function() {
            const id = this.dataset.id;
            const cb = document.querySelector(`#addExpJobCheckboxPool .addexp-job-check[value="${id}"]`);
            if (!cb) return;
            cb.checked = !cb.checked;
            this.classList.toggle('selected', cb.checked);
            syncAddExpTags();
        });
    });

    function filterAddExpJobs(val) {
        openAddExpJobDropdown();
        const v = val.toLowerCase().trim();
        let any = false;
        document.querySelectorAll('.addexp-job-visual-option').forEach(opt => {
            const searchData = (opt.dataset.search || '').toLowerCase();
            const match = !v || searchData.includes(v);
            opt.style.display = match ? 'flex' : 'none';
            if (match) any = true;
        });
        document.getElementById('addExpJobNoResults').style.display = any ? 'none' : 'block';
    }

    function syncAddExpTags() {
        const checked = [...document.querySelectorAll('#addExpJobCheckboxPool .addexp-job-check:checked')];
        addExpJobTags.innerHTML = '';
        const ids = [];

        checked.forEach(cb => {
            ids.push(cb.value);

            const opt = document.querySelector(`.addexp-job-visual-option[data-id="${cb.value}"]`);
            if (opt) opt.classList.add('selected');

            const tag = document.createElement('span');
            tag.style.cssText = 'display:inline-flex;align-items:center;gap:5px;' +
                'background:var(--primary-light, #eff6ff);color:var(--primary);' +
                'border:1px solid var(--primary);border-radius:20px;' +
                'padding:3px 10px;font-size:12px;font-weight:600;' +
                "font-family:'Inter',sans-serif";
            tag.innerHTML = cb.dataset.ref +
                `<button type="button" onclick="removeAddExpJob('${cb.value}')"
                style="background:none;border:none;cursor:pointer;
                       color:var(--primary);font-size:15px;line-height:1;padding:0">
                &times;
            </button>`;
            addExpJobTags.appendChild(tag);
        });

        document.querySelectorAll('.addexp-job-visual-option').forEach(opt => {
            if (!ids.includes(opt.dataset.id)) opt.classList.remove('selected');
        });

        const count = ids.length;
        addExpBadge.style.display = count > 0 ? 'inline-flex' : 'none';
        addExpBadge.textContent = count + (count === 1 ? ' job selected' : ' jobs selected');
    }

    function removeAddExpJob(id) {
        const cb = document.querySelector(`#addExpJobCheckboxPool .addexp-job-check[value="${id}"]`);
        if (cb) {
            cb.checked = false;
            syncAddExpTags();
        }
    }

    function clearAllAddExpJobs() {
        document.querySelectorAll('#addExpJobCheckboxPool .addexp-job-check').forEach(cb => cb.checked = false);
        document.querySelectorAll('.addexp-job-visual-option').forEach(opt => opt.classList.remove('selected'));
        syncAddExpTags();
        document.getElementById('addExpJobSearch').value = '';
        filterAddExpJobs('');
    }

    // ✅ Add hidden inputs before form submission
    // Replace 'additionalExpenseForm' with your actual form ID
    const additionalExpenseForm = document.querySelector('form'); // Adjust selector to your form
    if (additionalExpenseForm) {
        additionalExpenseForm.addEventListener('submit', function(e) {
            const form = this;
            const checkedJobs = document.querySelectorAll('#addExpJobCheckboxPool .addexp-job-check:checked');

            // Remove previously added hidden inputs
            form.querySelectorAll('input[name="job_ids[]"]').forEach(inp => inp.remove());

            // Add new hidden inputs
            checkedJobs.forEach(cb => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'job_ids[]';
                hidden.value = cb.value;
                form.appendChild(hidden);
            });

            console.log('Submitting additional expense with job_ids:', [...checkedJobs].map(cb => cb.value));
        });
    }

    // Add this to your additional expense form page
    document.querySelector('form').addEventListener('submit', function(e) {
        const form = this;
        const checkedJobs = document.querySelectorAll('#addExpJobCheckboxPool .addexp-job-check:checked');

        console.log('Submitting with jobs:', [...checkedJobs].map(cb => cb.value));

        // Remove old hidden inputs
        form.querySelectorAll('input[name="job_ids[]"]').forEach(inp => inp.remove());

        // Add new hidden inputs
        checkedJobs.forEach(cb => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'job_ids[]';
            hidden.value = cb.value;
            form.appendChild(hidden);
        });
    });
</script>
@endpush