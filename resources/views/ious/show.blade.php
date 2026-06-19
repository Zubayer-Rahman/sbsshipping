@extends('layouts.app')
@section('title','IOU Details')
@section('page-title','IOU Details')
@section('breadcrumb','IOUs / IOU Details')

@section('content')
<style>
    .iou-show-container {
        padding: 2rem;
        max-width: 1200px;
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

    .alert {
        padding: 1rem 1.25rem;
        border-radius: var(--radius-sm);
        margin-bottom: 1.5rem;
        font-family: 'Inter', sans-serif;
    }

    .alert-success {
        background: #d1fae5;
        border-left: 4px solid var(--success);
        color: #065f46;
    }

    .alert-error {
        background: #fee2e2;
        border-left: 4px solid var(--danger);
        color: #991b1b;
    }

    .iou-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 2rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 1.5rem;
    }

    .iou-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid var(--border);
    }

    .iou-ref {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        font-family: 'Inter', sans-serif;
    }

    .iou-date {
        color: var(--text-muted);
        font-size: 0.938rem;
        font-family: 'Inter', sans-serif;
    }

    .badge {
        display: inline-block;
        padding: 0.375rem 0.875rem;
        border-radius: 999px;
        font-size: 0.813rem;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
        margin-left: 0.5rem;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-secondary {
        background: #e2e8f0;
        color: #475569;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }

    .info-item-label {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-bottom: 0.375rem;
        font-family: 'Inter', sans-serif;
    }

    .info-item-value {
        font-weight: 600;
        color: var(--text-primary);
        font-family: 'Inter', sans-serif;
    }

    .info-item-amount {
        font-size: 1.5rem;
        font-weight: 700;
        font-family: 'Inter', sans-serif;
    }

    .amount-total {
        color: var(--text-primary);
    }

    .amount-paid {
        color: var(--success);
    }

    .amount-balance {
        color: var(--danger);
    }

    .text-overdue {
        color: var(--danger);
        font-weight: 600;
    }

    .description-section {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border);
    }

    .document-link {
        color: var(--primary);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        font-weight: 500;
        transition: color 0.2s;
    }

    .document-link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    .action-buttons {
        margin-top: 2rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-sm);
        font-weight: 500;
        font-family: 'Inter', sans-serif;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        box-shadow: 0 4px 12px var(--primary-glow);
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        background: #059669;
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        font-family: 'Inter', sans-serif;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Inter', sans-serif;
    }

    .data-table thead {
        background: var(--body-bg);
    }

    .data-table th {
        padding: 0.875rem 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
    }

    .data-table td {
        padding: 0.875rem 1rem;
        border-top: 1px solid var(--border);
    }

    .data-table tfoot {
        background: var(--body-bg);
        font-weight: 600;
    }

    .text-right {
        text-align: right;
    }

    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
        color: var(--text-muted);
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-overlay.hidden {
        display: none;
    }

    .modal-content {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        box-shadow: var(--shadow-md);
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        font-family: 'Inter', sans-serif;
    }

    .form-group {
        margin-bottom: 1.25rem;
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
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: 'Inter', sans-serif;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .help-text {
        margin-top: 0.375rem;
        font-size: 0.875rem;
        color: var(--text-muted);
        font-family: 'Inter', sans-serif;
    }

    .modal-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .modal-actions .btn {
        flex: 1;
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
</style>

<div class="iou-show-container">
    <div class="page-header">
        <h1 class="page-title">IOU Details</h1>
        <a href="{{ route('ious.index') }}" class="back-link">← Back to List</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <!-- IOU Header -->
    <div class="iou-card">
        <div class="iou-card-header">
            <div>
                <h2 class="iou-ref">{{ $iou->reference_number }}</h2>
                <p class="iou-date">Created on {{ $iou->created_at->format('d M Y') }}</p>
            </div>
            <div>
                <span class="badge {{ $iou->type == 'receivable' ? 'badge-success' : 'badge-danger' }}">
                    {{ ucfirst($iou->type) }}
                </span>
                @if($iou->status == 'paid')
                <span class="badge badge-success">Paid</span>
                @elseif($iou->status == 'partial')
                <span class="badge badge-warning">Partial</span>
                @else
                <span class="badge badge-secondary">Pending</span>
                @endif
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <p class="info-item-label">Contact</p>
                <p class="info-item-value">{{ $iou->contact->name }}</p>
            </div>
            <div class="info-item">
                <p class="info-item-label">Against</p>
                <div class="info-item-value" style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @if($iou->jobs && $iou->jobs->count() > 0)
                    @foreach($iou->jobs as $job)
                    <span style="background: var(--primary-light); 
                             color: var(--primary); 
                             padding: 4px 12px; 
                             border-radius: 20px; 
                             font-size: 12px; 
                             font-weight: 700;
                             border: 1px solid var(--primary);
                             display: inline-flex;
                             align-items: center;">
                        {{ $job->job_id ?? $job->job_no }}
                    </span>
                    @endforeach
                    @elseif($iou->against)
                    {{ $iou->against }}
                    @else
                    -
                    @endif
                </div>
            </div>
            <div class="info-item">
                <p class="info-item-label">Total Amount</p>
                <p class="info-item-amount amount-total">৳{{ number_format($iou->amount, 2) }}</p>
            </div>
            <div class="info-item">
                <p class="info-item-label">Paid Amount</p>
                <p class="info-item-amount amount-paid">৳{{ number_format($iou->paid_amount, 2) }}</p>
            </div>
            <div class="info-item">
                <p class="info-item-label">Balance</p>
                <p class="info-item-amount amount-balance">৳{{ number_format($iou->balance, 2) }}</p>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Payment Date <span class="required">*</span></label>
                {{-- We use $iou->created_at and format it for the date input (Y-m-d) --}}
                <p class="info-item-value">
                    <span style="background: var(--primary-light); 
                     color: var(--primary); 
                     padding: 6px 12px; 
                     border-radius: var(--radius-sm); 
                     font-weight: 700; 
                     display: inline-block;
                     border: 1px solid var(--primary-glow);">
                        {{ $iou->created_at->format('d M Y') }}
                    </span>
                </p>
            </div>
        </div>

        @if($iou->is_released)
        <div class="description-section">
            <h4 style="color: var(--success); font-weight: 600; margin-bottom: 1rem;">✓ This IOU has been released</h4>
            <div class="info-grid">
                <div class="info-item">
                    <p class="info-item-label">Released On</p>
                    <p class="info-item-value">{{ $iou->released_at ? \Carbon\Carbon::parse($iou->released_at)->format('d M Y') : '-' }}</p>
                </div>
                <div class="info-item">
                    <p class="info-item-label">Released By</p>
                    <p class="info-item-value">{{ $iou->releasedBy->name ?? '-' }}</p>
                </div>
                <div class="info-item">
                    <p class="info-item-label">Related Expense</p>
                    <p class="info-item-value">
                        @if($iou->expense)
                        <a href="{{ route('ious.expense-list', $iou->expense_id) }}" class="document-link">
                            View Expense Record →
                        </a>
                        @else
                        -
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if($iou->description)
        <div class="description-section">
            <p class="info-item-label">Description</p>
            <p class="info-item-value">{{ $iou->description }}</p>
        </div>
        @endif

        @if($iou->document)
        <div class="description-section">
            <p class="info-item-label">Supporting Document</p>
            <a href="{{ Storage::url($iou->document) }}" target="_blank" class="document-link">
                📄 View Document
            </a>
        </div>
        @endif

        @if(!$iou->is_released)
        <div class="action-buttons">
            <a href="{{ route('ious.edit', $iou) }}" class="btn btn-primary">Edit IOU</a>
            <!-- @if($iou->balance > 0)
            <a href="{{ route('ious.release', $iou) }}" class="btn btn-success">Release IOU</a>
            @endif -->
            <button onclick="document.getElementById('payment-modal').classList.remove('hidden')"
                class="btn btn-success">Release Payment</button>
            <form action="{{ route('ious.destroy', $iou) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this IOU?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
        @endif

        <!-- @if($iou->status != 'paid')
        <div class="action-buttons">
            <a href="{{ route('ious.edit', $iou) }}" class="btn btn-primary">Edit IOU</a>
            <button onclick="document.getElementById('payment-modal').classList.remove('hidden')"
                class="btn btn-success">Add Payment</button>
            <form action="{{ route('ious.destroy', $iou) }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete this IOU?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
        @endif -->
    </div>

    <!-- Payment History -->
    <div class="iou-card">
        <h3 class="section-title">Payment History</h3>

        @if($iou->payments->count() > 0)
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-right">Amount</th>
                        <th>Method</th>
                        <th>Notes</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($iou->payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                        <td class="text-right" style="font-weight: 600;">৳{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_method ?? '-' }}</td>
                        <td>{{ $payment->notes ?? '-' }}</td>
                        <td>{{ $payment->creator->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total Paid:</td>
                        <td class="text-right" style="color: var(--success);">৳{{ number_format($iou->paid_amount, 2) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="empty-state">No payments recorded yet.</div>
        @endif
    </div>
</div>

<!-- Payment Modal -->
<div id="payment-modal" class="modal-overlay hidden">
    <div class="modal-content">
        <h3 class="modal-title">Add Payment</h3>

        <form action="{{ route('ious.payment', $iou) }}" method="POST">
            @csrf

            <!-- Payment Amount -->
            <div class="form-group">
                <label class="form-label">Payment Amount <span class="required">*</span></label>
                <input type="number" name="amount" step="0.01" min="0.01" value="{{ $iou->balance > 0 ? $iou->balance : '' }}" required class="form-control">

                <div style="margin-top: 8px; display: flex; justify-content: space-between; font-size: 12px;">
                    <span style="color: var(--text-muted);">
                        {{ $iou->type == 'receivable' ? 'Receivable Balance:' : 'Payable Balance:' }}
                        <strong style="color: var(--primary);">৳{{ number_format(max(0, $iou->balance), 2) }}</strong>
                    </span>

                    {{-- Show Extra Amount if balance is negative or payments exceed amount --}}
                    @php
                    $totalPayments = $iou->payments->sum('amount');
                    $extra = $totalPayments > $iou->amount ? $totalPayments - $iou->amount : 0;
                    @endphp

                    @if($extra > 0)
                    <span style="color: var(--danger); font-weight: 700;">
                        Extra Expensed: ৳{{ number_format($extra, 2) }}
                    </span>
                    @endif
                </div>
            </div>

            <!-- Job Number & Client Name Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.25rem;">
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

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Client Name</label>
                    <select name="client_id" class="form-control">
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Date & Method Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.25rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Payment Date <span class="required">*</span></label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}"
                        required class="form-control">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_account_id" class="form-control">
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_name }} (৳{{ number_format($account->current_balance, 2) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="2" class="form-control" placeholder="Add any details about this specific payment..."></textarea>
            </div>

            {{-- Hidden job checkboxes — inside form so they submit --}}
            <div id="jobCheckboxPool" style="display:none">
                @foreach($jobs as $job)
                @php $ref = $job->job_no ?? $job->job_id; @endphp
                <input type="checkbox" name="job_ids[]"
                    value="{{ $job->id }}"
                    data-ref="{{ $ref }}"
                    class="job-check">
                @endforeach
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn btn-success">Add Payment</button>
                <button type="button" onclick="document.getElementById('payment-modal').classList.add('hidden')"
                    class="btn btn-danger">Cancel</button>
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
        </form>
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

    // ── Total amount display ──────────────────────────────────────────────────────
    document.getElementById('totalAmount').addEventListener('input', function() {
        const v = parseFloat(this.value) || 0;
        document.getElementById('totalAmountDisplay').textContent = v.toFixed(2);
    });
</script>
@endpush