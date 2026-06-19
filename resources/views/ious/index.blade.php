@extends('layouts.app')
@section('title','Add IOU')
@section('page-title','Add IOU')
@section('breadcrumb','IOUs / Add IOU')

@section('content')
<style>
    .iou-container {
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .iou-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .iou-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        font-family: 'Inter', sans-serif;
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

    .btn-secondary {
        background: var(--text-muted);
        color: white;
    }

    .btn-secondary:hover {
        background: #475569;
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

    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .summary-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        border-left: 4px solid;
    }

    .summary-card.receivable {
        border-left-color: var(--success);
    }

    .summary-card.payable {
        border-left-color: var(--danger);
    }

    .summary-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .summary-card-label {
        color: var(--text-muted);
        font-size: 0.875rem;
        font-family: 'Inter', sans-serif;
    }

    .summary-card-amount {
        font-size: 1.875rem;
        font-weight: 700;
        margin-top: 0.5rem;
        font-family: 'Inter', sans-serif;
    }

    .summary-card.receivable .summary-card-amount {
        color: var(--success);
    }

    .summary-card.payable .summary-card-amount {
        color: var(--danger);
    }

    .summary-icon {
        font-size: 2.5rem;
    }

    .filter-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        margin-bottom: 1.5rem;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
        font-weight: 500;
        font-size: 0.875rem;
        font-family: 'Inter', sans-serif;
    }

    .form-control {
        width: 100%;
        padding: 0.625rem 0.875rem;
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

    .table-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
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
        padding: 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
    }

    .data-table td {
        padding: 1rem;
        border-top: 1px solid var(--border);
    }

    .data-table tbody tr {
        transition: background 0.2s;
    }

    .data-table tbody tr:hover {
        background: var(--primary-light);
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
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

    .text-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .text-link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .font-semibold {
        font-weight: 600;
    }

    .text-overdue {
        color: var(--danger);
        font-weight: 600;
    }

    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
        color: var(--text-muted);
    }

    .pagination {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
    }

    .btn-group {
        display: flex;
        gap: 0.5rem;
    }

    /* confirmation modal styles */
    /* Modal Backdrop */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 31, 75, 0.5);
        /* Semi-transparent version of your sidebar-bg */
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
    }

    .modal-backdrop.hidden {
        display: none;
    }

    /* Modal Box */
    .modal-box {
        background: white;
        padding: 2rem;
        border-radius: var(--radius);
        max-width: 400px;
        width: 90%;
        text-align: center;
        box-shadow: var(--shadow-md);
        animation: modalSlideIn 0.3s ease-in-out;
    }

    @keyframes modalSlideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .modal-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .modal-text {
        color: var(--text-muted);
        font-size: 0.938rem;
        line-height: 1.5;
        margin-bottom: 1.5rem;
    }

    .modal-footer {
        display: flex;
        gap: 12px;
    }

    .btn-modal-cancel {
        flex: 1;
        padding: 10px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        background: var(--danger);
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .btn-modal-confirm {
        flex: 1;
        padding: 10px;
        border-radius: var(--radius-sm);
        border: none;
        background: var(--success);
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .btn-modal-confirm:hover {
        filter: brightness(0.9);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
</style>

<div class="iou-container">
    <div class="iou-header">
        <h1 class="iou-title">IOU Management</h1>
        <a href="{{ route('ious.create') }}" class="btn btn-primary">+ New IOU</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card receivable">
            <div class="summary-card-header">
                <div>
                    <p class="summary-card-label">Total Receivable (They Owe You)</p>
                    <p class="summary-card-amount">৳{{ number_format($totalReceivable, 2) }}</p>
                </div>
                <div class="summary-icon">↓</div>
            </div>
        </div>

        <div class="summary-card payable">
            <div class="summary-card-header">
                <div>
                    <p class="summary-card-label">Total Payable (You Owe Them)</p>
                    <p class="summary-card-amount">৳{{ number_format($totalPayable, 2) }}</p>
                </div>
                <div class="summary-icon">↑</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET">
            <div class="filter-grid">
                <div class="form-group">
                    <input type="text" name="search" placeholder="Search..."
                        value="{{ request('search') }}" class="form-control">
                </div>
                <div class="form-group">
                    <select name="type" class="form-control">
                        <option value="all">All Types</option>
                        <option value="receivable" {{ request('type') == 'receivable' ? 'selected' : '' }}>Receivable</option>
                        <option value="payable" {{ request('type') == 'payable' ? 'selected' : '' }}>Payable</option>
                    </select>
                </div>
                <div class="form-group">
                    <select name="status" class="form-control">
                        <option value="all">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="form-group btn-group">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('ious.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- IOUs Table -->
    <div class="table-card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ref #</th>
                        <th>Contact</th>
                        <th>Type</th>
                        <th>Against</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Due Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ious as $iou)
                    <tr onclick="window.location='{{ route('ious.show', $iou) }}'"
                        style="cursor: pointer;"
                        class="hover:bg-gray-50">

                        <td>
                            <span class="text-link font-semibold">{{ $iou->reference_number }}</span>
                        </td>
                        <td>{{ $iou->contact->name }}</td>
                        <td>
                            <span class="badge {{ $iou->type == 'receivable' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($iou->type) }}
                            </span>
                        </td>

                        <td>
                            @if($iou->jobs && $iou->jobs->count() > 0)
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                @foreach($iou->jobs as $job)
                                <span style="font-size: 11px; background: var(--primary-light); color: var(--primary); padding: 2px 6px; border-radius: 4px; font-weight: 600; border: 1px solid var(--primary-glow);">
                                    {{ $job->job_no ?? $job->job_id }}
                                </span>
                                @endforeach
                            </div>
                            @else
                            <span style="color: var(--text-muted);">{{ $iou->against ?? '-' }}</span>
                            @endif
                        </td>

                        <td class="">৳{{ number_format($iou->amount, 2) }}</td>
                        <td class="">৳{{ number_format($iou->paid_amount, 2) }}</td>
                        <td class="font-semibold">৳{{ number_format($iou->balance, 2) }}</td>

                        <td class="text-center">
                            @if($iou->status == 'paid')
                            <span class="badge badge-success">Paid</span>
                            @elseif($iou->status == 'partial')
                            <span class="badge badge-warning">Partial</span>
                            @else
                            <span class="badge badge-secondary">Pending</span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($iou->due_date)
                            <span class="{{ $iou->due_date->isPast() && $iou->status != 'paid' ? 'text-overdue' : '' }}">
                                {{ $iou->due_date->format('d M Y') }}
                            </span>
                            @else
                            -
                            @endif
                        </td>

                        <td class="text-center">
                            <div style="display: flex; justify-content: center; gap: 12px; align-items: center;">
                                {{-- View Action --}}
                                <a href="{{ route('ious.show', $iou) }}" class="text-link" onclick="event.stopPropagation();">View</a>

                                @if(!$iou->is_released)
                                {{-- Edit Action --}}
                                <a href="{{ route('ious.edit', $iou) }}" class="text-link" onclick="event.stopPropagation();">Edit</a>

                                {{-- Release Action - Triggers the custom Modal --}}
                                <button type="button"
                                    onclick="event.stopPropagation(); openReleaseModal('{{ $iou->id }}', '{{ $iou->reference_number }}')"
                                    style="background: none; border: none; color: var(--success); cursor: pointer; font-weight: 600; padding: 0; font-family: 'Inter', sans-serif; font-size: 14px;">
                                    Release
                                </button>
                                @else
                                {{-- Status Badge --}}
                                <span class="badge badge-success" style="opacity: 0.8;">Released</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="empty-state">
                            No IOUs found. <a href="{{ route('ious.create') }}" class="text-link">Create one</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Custom Release Confirmation Modal -->
            <div id="releaseConfirmModal" class="modal-backdrop hidden">
                <div class="modal-box">
                    <div class="modal-icon">⚠️</div>
                    <h3 class="modal-title">Confirm Release</h3>
                    <p class="modal-text">
                        Are you sure you want to release <strong id="displayIouRef"></strong>?<br>
                        This will finalize the record and move it to the release list.
                    </p>

                    <form id="confirmReleaseForm" method="POST">
                        @csrf
                        <div class="modal-footer">
                            <button type="button" onclick="closeReleaseModal()" class="btn-modal-cancel">Cancel</button>
                            <button type="submit" class="btn-modal-confirm">Confirm & Release</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination (under maintainance, will implement later) -->
    <!-- <div class="pagination">
        {{ $ious->links() }}
    </div> -->
</div>
@endsection

@push('scripts')
<script>
    function openReleaseModal(iouId, iouRef) {
        const modal = document.getElementById('releaseConfirmModal');
        const form = document.getElementById('confirmReleaseForm');
        const refDisplay = document.getElementById('displayIouRef');

        // 1. Set the display text
        refDisplay.innerText = iouRef;

        // 2. Set the form action URL dynamically
        // Using the same URL pattern we defined in web.php
        form.action = `/ious/${iouId}/release-instant`;

        // 3. Show the modal
        modal.classList.remove('hidden');
    }

    function closeReleaseModal() {
        document.getElementById('releaseConfirmModal').classList.add('hidden');
    }

    // Close modal if user clicks on the backdrop
    window.onclick = function(event) {
        const modal = document.getElementById('releaseConfirmModal');
        if (event.target == modal) {
            closeReleaseModal();
        }
    }
</script>
@endpush