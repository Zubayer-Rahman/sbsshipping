@extends('layouts.app')
@section('title','Add IOU')
@section('page-title','Add IOU')
@section('breadcrumb','IOUs / Add IOU')

@section('content')
<style>
    .iou-create-container {
        padding: 2rem;
        max-width: 1024px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        font-family: 'Inter', sans-serif;
    }

    .back-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .form-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 2rem;
        box-shadow: var(--shadow-md);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
        font-weight: 500;
        font-family: 'Inter', sans-serif;
    }

    .required {
        color: var(--danger);
    }

    .form-control {
        width: 440px;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: 'Inter', sans-serif;
        font-size: 0.938rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .form-control.error {
        border-color: var(--danger);
    }

    .form-control:disabled {
        background: var(--body-bg);
        cursor: not-allowed;
    }

    .help-text {
        margin-top: 0.375rem;
        font-size: 0.875rem;
        color: var(--text-muted);
        font-family: 'Inter', sans-serif;
    }

    .error-text {
        margin-top: 0.375rem;
        font-size: 0.875rem;
        color: var(--danger);
        font-family: 'Inter', sans-serif;
    }

    .radio-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .radio-option {
        padding: 1rem;
        border: 2px solid var(--border);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
    }

    .radio-option:hover {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .radio-option input[type="radio"] {
        margin-right: 0.75rem;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .radio-option input[type="radio"]:checked+.radio-content {
        color: var(--primary);
    }

    .radio-content-title {
        font-weight: 600;
        font-family: 'Inter', sans-serif;
    }

    .radio-content-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-family: 'Inter', sans-serif;
    }

    .receivable .radio-content-title {
        color: var(--success);
    }

    .payable .radio-content-title {
        color: var(--danger);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-sm);
        font-weight: 500;
        font-family: 'Inter', sans-serif;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        flex: 1;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        box-shadow: 0 4px 12px var(--primary-glow);
    }

    .btn-secondary {
        background: var(--text-muted);
        color: white;
    }

    .btn-secondary:hover {
        background: #475569;
    }

    /* Job dropdown styles */
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
</style>

<div class="iou-create-container">
    <div class="page-header">
        <h1 class="page-title">Create New IOU</h1>
        <a href="{{ route('ious.index') }}" class="back-link">← Back to List</a>
    </div>

    <div class="form-card">
        <form action="{{ route('ious.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="display: flex; justify-content: space-between;">
                <!-- Reference Number -->
                <div class="form-group">
                    <label class="form-label">Reference Number</label>
                    <input type="text" value="{{ $referenceNumber }}" disabled class="form-control">
                    <p class="help-text">Auto-generated</p>
                </div>

                <!-- Contact -->
                <div class="form-group">
                    <label class="form-label">Contact <span class="required">*</span></label>
                    <select name="contact_id" required class="form-control @error('contact_id') error @enderror">
                        <option value="">Select Contact</option>
                        @foreach($contacts as $contact)
                        <option value="{{ $contact->id }}" {{ old('contact_id') == $contact->id ? 'selected' : '' }}>
                            {{ $contact->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('contact_id')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Type -->
            <div class="form-group">
                <label class="form-label">Type <span class="required">*</span></label>
                <div class="radio-group">
                    <label class="radio-option receivable">
                        <input type="radio" name="type" value="receivable" {{ old('type') == 'receivable' ? 'checked' : '' }} required>
                        <div class="radio-content">
                            <div class="radio-content-title">Receivable</div>
                            <div class="radio-content-desc">They owe you</div>
                        </div>
                    </label>
                    <label class="radio-option payable">
                        <input type="radio" name="type" value="payable" {{ old('type') == 'payable' ? 'checked' : '' }} required>
                        <div class="radio-content">
                            <div class="radio-content-title">Payable</div>
                            <div class="radio-content-desc">You owe them</div>
                        </div>
                    </label>
                </div>
                @error('type')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; gap: 20px;">
                <!-- Amount -->
                <div class="form-group">
                    <label class="form-label">Amount <span class="required">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}"
                        required class="form-control @error('amount') error @enderror">
                    @error('amount')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                {{-- JOB Number --}}
                <div class="form-group" style="width: 100%;">
                    <label class="form-label" style="font-weight:700">
                        JOB Number
                        <!-- <span style="font-weight:400;color:var(--text-muted);font-size:12px">
                            (No Need To Select Job No. If Office Expenses)
                        </span> -->
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
                        <!-- <span>Leave empty to autogenerate · Multiple jobs can be selected</span> -->
                        <span id="jobSelectedCount"
                            style="display:none;background:var(--primary);color:#fff;
                                 font-size:11px;font-weight:700;padding:2px 10px;
                                 border-radius:20px;white-space:nowrap">
                        </span>
                    </div>
                </div>
            </div>


            <!-- payment account -->
            <div class="form-group" style="width: auto !important;"
                <label class="form-label">Payment Account:</label>
                <div style="display:flex;align-items:center;border-radius:var(--radius-sm);overflow:hidden">
                    <select name="payment_account_id" required class="form-control">
                        @foreach(\App\Models\PaymentAccount::where('is_active', true)->orderBy('account_name')->get() as $acc)
                        <option value="{{ $acc->id }}" {{ old('payment_account_id') == $acc->id ? 'selected' : '' }}>
                            {{ $acc->account_name }} (Balance: ৳{{ number_format($acc->current_balance, 2) }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" style="width: 100% !important;" class="form-control @error('description') error @enderror">{{ old('description') }}</textarea>
                @error('description')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; justify-content: space-between;">
                <!-- Payment Date -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Payment Date <span class="required">*</span></label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}"
                        required class="form-control">
                </div>

                <!-- Document -->
                <div class="form-group">
                    <label class="form-label">Supporting Document</label>
                    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"
                        class="form-control @error('document') error @enderror">
                    <p class="help-text">PDF, JPG, PNG (Max 2MB)</p>
                    @error('document')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Hidden job checkboxes — inside form so they submit --}}
            <div id="jobCheckboxPool" style="display:none">
                @foreach($jobs as $job)
                @php $ref = $job->job_no ?? $job->job_id; @endphp
                <input type="checkbox"
                    value="{{ $job->id }}"
                    data-ref="{{ $ref }}"
                    class="job-check"> <!-- REMOVED name="job_ids[]" -->
                @endforeach
            </div>

            <!-- Submit Buttons -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create IOU</button>
                <a href="{{ route('ious.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

{{-- Floating job dropdown — body level, never clipped --}}
<div id="jobFloatingDropdown"
    style="display:none;position:fixed;background:#fff;
            border:1.5px solid var(--primary);border-radius:var(--radius-sm);
            box-shadow:0 8px 28px rgba(15,31,75,.14);
            max-height:280px;z-index:9999;flex-direction:column;overflow:hidden">

    <div style="display:flex;justify-content:space-between;align-items:center;
                padding:8px 14px;border-bottom:1px solid var(--border);
                background:var(--body-bg);flex-shrink:0">
        <span style="font-size:11px;font-weight:700;color:var(--text-muted);
                     text-transform:uppercase;letter-spacing:.06em">Select Jobs</span>
        <div style="display:flex;gap:12px">
            <button type="button" onclick="selectAllJobs()"
                style="font-size:12px;color:var(--primary);background:none;
                           border:none;cursor:pointer;font-weight:700;padding:0;
                           font-family:'Inter',sans-serif">
                Select All
            </button>
            <button type="button" onclick="clearAllJobs()"
                style="font-size:12px;color:var(--danger);background:none;
                           border:none;cursor:pointer;font-weight:700;padding:0;
                           font-family:'Inter',sans-serif">
                Clear
            </button>
        </div>
    </div>

    <div id="jobVisualList" style="overflow-y:auto;flex:1">
        @foreach($jobs as $job)
        @php $ref = $job->job_no ?? $job->job_id; @endphp
        <div class="job-visual-option" data-ref="{{ $ref }}" data-id="{{ $job->id }}"
            style="display:flex;align-items:center;gap:10px;padding:9px 14px;
                    cursor:pointer;border-bottom:1px solid var(--border);transition:background .12s">
            <span class="job-visual-check"
                style="width:16px;height:16px;border:2px solid var(--border);
                         border-radius:3px;flex-shrink:0;display:flex;
                         align-items:center;justify-content:center;
                         transition:all .15s;background:#fff">
            </span>
            <span style="font-size:13px;font-weight:600;color:var(--primary);
                         font-family:'Inter',sans-serif">{{ $ref }}</span>
            @if(!empty($job->client_name))
            <span style="font-size:12px;color:var(--text-muted);margin-left:auto;
                             max-width:180px;overflow:hidden;text-overflow:ellipsis;
                             white-space:nowrap;font-family:'Inter',sans-serif">
                {{ $job->client_name }}
            </span>
            @endif
        </div>
        @endforeach
    </div>

    <div id="jobNoResults"
        style="display:none;padding:20px;text-align:center;font-size:13px;
                color:var(--text-muted);font-family:'Inter',sans-serif">
        No jobs found
    </div>
</div>
@endsection

@push('scripts')
<script>
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

        document.getElementById('jobIdSingle').value = ids.join(',');
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

    // ── Total amount display ──────────────────────────────────────────────────────
    document.getElementById('totalAmount').addEventListener('input', function() {
        const v = parseFloat(this.value) || 0;
        document.getElementById('totalAmountDisplay').textContent = v.toFixed(2);
    });
</script>
@endpush