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
                <p class="info-item-value">{{ $iou->against ?? '-' }}</p>
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
            <div class="info-item">
                <p class="info-item-label">Due Date</p>
                <p class="info-item-value {{ $iou->due_date && $iou->due_date->isPast() && $iou->status != 'paid' ? 'text-overdue' : '' }}">
                    {{ $iou->due_date ? $iou->due_date->format('d M Y') : '-' }}
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
                        <a href="{{ route('expenses.list', $iou->expense_id) }}" class="document-link">
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
            @if($iou->balance > 0)
            <a href="{{ route('ious.release', $iou) }}" class="btn btn-success">Release IOU</a>
            @endif
            <button onclick="document.getElementById('payment-modal').classList.remove('hidden')"
                class="btn btn-success">Add Payment</button>
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

            <div class="form-group">
                <label class="form-label">Payment Amount <span class="required">*</span></label>
                <input type="number" name="amount" step="0.01" min="0.01" max="{{ $iou->balance }}"
                    required class="form-control">
                <p class="help-text">Maximum: ৳{{ number_format($iou->balance, 2) }}</p>
            </div>

            <div class="form-group">
                <label class="form-label">Payment Date <span class="required">*</span></label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}"
                    required class="form-control">
            </div>

            <div class="form-group">
                <label class="form-label">Payment Method</label>
                <select name="payment_method" class="form-control">
                    <option value="">Select Method</option>
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="check">Check</option>
                    <option value="mobile_banking">Mobile Banking</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="2" class="form-control"></textarea>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn btn-success">Add Payment</button>
                <button type="button" onclick="document.getElementById('payment-modal').classList.add('hidden')"
                    class="btn btn-danger">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection