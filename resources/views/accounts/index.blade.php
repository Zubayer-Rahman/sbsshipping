@extends('layouts.app')
@section('title','Payment Accounts')
@section('page-title','Payment Accounts')
@section('breadcrumb','Accounts / List')

@section('content')
<style>
    .accounts-container {
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
        font-family: 'Inter', sans-serif;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    /* Table Styles to match Cash Flow */
    .table-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        border: 1px solid var(--border);
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background: var(--body-bg);
    }

    .data-table th {
        padding: 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border);
    }

    .data-table td {
        padding: 1.25rem 1rem;
        border-bottom: 1px solid var(--border);
        font-size: 0.938rem;
        color: var(--text-primary);
        vertical-align: middle;
    }

    .data-table tbody tr {
        transition: background 0.2s;
        cursor: pointer;
    }

    .data-table tbody tr:hover {
        background: var(--primary-light);
    }

    /* descriptive column styles */
    .account-main {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .account-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: var(--body-bg);
    }

    .account-info-text {
        display: flex;
        flex-direction: column;
    }

    .account-title {
        font-weight: 700;
        color: var(--primary);
    }

    .account-subtitle {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .balance-positive {
        color: var(--success);
        font-weight: 700;
    }

    .balance-neutral {
        color: var(--primary);
        font-weight: 700;
    }

    .badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-active {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .text-right {
        text-align: right;
    }

    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: var(--radius-sm);
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-size: 0.875rem;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-outline {
        border: 1px solid var(--border);
        background: white;
        color: var(--text-muted);
    }

    .btn-outline:hover {
        background: var(--body-bg);
        color: var(--text-primary);
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: var(--radius-sm);
        margin-bottom: 1.5rem;
        background: #d1fae5;
        border-left: 4px solid var(--success);
        color: #065f46;
    }
</style>

<div class="accounts-container">
    <div class="page-header">
        <h1 class="page-title">Payment Accounts</h1>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('accounts.cashflow') }}" class="btn btn-outline">📊 View Cash Flow</a>
            {{-- ADD THESE TWO BUTTONS --}}
            <button onclick="openDepositModal()" class="btn" style="background:#10b981;color:white;">
                💰 Add Cash
            </button>
            <button onclick="openTransferModal()" class="btn" style="background:#6366f1;color:white;">
                🔄 Transfer
            </button>
            <a href="{{ route('accounts.create') }}" class="btn btn-primary">+ Add New Account</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert">{{ session('success') }}</div>
    @endif

    <div class="table-card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Account Details</th>
                        <!-- <th>Account Type</th> -->
                        <th class="text-right">Opening Balance</th>
                        <th class="text-right">Current Balance</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                    <tr onclick="window.location='{{ route('accounts.show', $account) }}'">
                        <td>
                            <div class="account-main">
                                <div class="account-icon">
                                    @if($account->account_type == 'bank') 🏦
                                    @elseif($account->account_type == 'cash') 💵
                                    @elseif($account->account_type == 'mobile_banking') 📱
                                    @else 💳 @endif
                                </div>
                                <div class="account-info-text">
                                    <span class="account-title">{{ $account->account_name }}</span>
                                    <span class="account-subtitle">
                                        {{ $account->bank_name ?? 'Local Account' }}
                                        {{ $account->account_number ? '• '.$account->account_number : '' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <!-- <td>
                            <span style="text-transform: capitalize;">{{ str_replace('_', ' ', $account->account_type) }}</span>
                        </td> -->
                        <td class="" style="color: var(--text-muted);">
                            ৳{{ number_format($account->opening_balance, 2) }}
                        </td>
                        <td class="">
                            <span class="balance-neutral">৳{{ number_format($account->current_balance, 2) }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $account->is_active ? 'badge-active' : 'badge-inactive' }}">
                                {{ $account->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="account-info-text">
                                <span>{{ $account->creator->name ?? 'System' }}</span>
                                <span style="font-size: 0.7rem; color: var(--text-muted);">{{ $account->created_at->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="text-right" style="display:flex;gap:0.5rem;" onclick="event.stopPropagation()">
                            {{-- ADD CASH button per row --}}
                            <button onclick="openDepositModal({{ $account->id }}, '{{ $account->account_name }}')"
                                class="btn" style="background:#10b981;color:white;padding:0.4rem 0.8rem;font-size:0.75rem;">
                                💰 Add Cash
                            </button>
                            {{-- TRANSFER button per row --}}
                            <button onclick="openTransferModal({{ $account->id }})"
                                class="btn" style="background:#6366f1;color:white;padding:0.4rem 0.8rem;font-size:0.75rem;">
                                🔄 Transfer
                            </button>
                            <a href="{{ route('accounts.show', $account) }}"
                                class="btn btn-outline" style="padding:0.4rem 0.8rem;font-size:0.75rem;">
                                View
                            </a>
                            <form action="{{ route('accounts.destroy', $account) }}" method="POST"
                                onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn"
                                    style="background:var(--danger);color:white;padding:0.4rem 0.8rem;font-size:0.75rem;">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            No accounts found. Click "Add New Account" to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



{{-- ===================== DEPOSIT MODAL ===================== --}}
<div id="depositModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);
     z-index:1000;align-items:center;justify-content:center;">
    <div style="background:var(--card-bg);border-radius:var(--radius);padding:2rem;
                width:100%;max-width:480px;box-shadow:var(--shadow-md);position:relative;">

        {{-- Close --}}
        <button onclick="closeDepositModal()"
            style="position:absolute;top:1rem;right:1rem;background:none;border:none;
                   font-size:1.25rem;cursor:pointer;color:var(--text-muted)">✕</button>

        <h2 style="margin:0 0 1.5rem;font-size:1.25rem;font-weight:700;color:var(--text-primary)">
            💰 Add Cash / Deposit
        </h2>

        <form action="" method="POST" id="depositForm">
            @csrf

            {{-- Account Dropdown --}}
            <div class="form-group" style="margin-bottom:1rem">
                <label class="form-label">To Account <span class="required">*</span></label>
                <select name="account_id" id="depositAccountSelect" required
                    class="form-control" onchange="updateDepositAction(this.value)">
                    @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" data-name="{{ $acc->account_name }}">
                        {{ $acc->account_name }} (৳{{ number_format($acc->current_balance, 2) }})
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Amount --}}
            <div class="form-group" style="margin-bottom:1rem">
                <label class="form-label">Amount <span class="required">*</span></label>
                <input type="number" name="amount" step="0.01" min="0.01" required
                    class="form-control" placeholder="0.00">
            </div>

            {{-- Description --}}
            <div class="form-group" style="margin-bottom:1rem">
                <label class="form-label">Description <span class="required">*</span></label>
                <input type="text" name="description" required class="form-control"
                    placeholder="e.g., Cash received from client">
            </div>

            {{-- Date --}}
            <div class="form-group" style="margin-bottom:1.5rem">
                <label class="form-label">Date <span class="required">*</span></label>
                <input type="date" name="date" required class="form-control"
                    value="{{ date('Y-m-d') }}">
            </div>

            <div style="display:flex;gap:0.75rem">
                <button type="submit" class="btn btn-primary" style="flex:1">
                    💰 Deposit
                </button>
                <button type="button" onclick="closeDepositModal()"
                    class="btn btn-outline" style="flex:1">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===================== TRANSFER MODAL ===================== --}}
<div id="transferModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);
     z-index:1000;align-items:center;justify-content:center;">
    <div style="background:var(--card-bg);border-radius:var(--radius);padding:2rem;
                width:100%;max-width:480px;box-shadow:var(--shadow-md);position:relative;">

        {{-- Close --}}
        <button onclick="closeTransferModal()"
            style="position:absolute;top:1rem;right:1rem;background:none;border:none;
                   font-size:1.25rem;cursor:pointer;color:var(--text-muted)">✕</button>

        <h2 style="margin:0 0 1.5rem;font-size:1.25rem;font-weight:700;color:var(--text-primary)">
            🔄 Transfer Between Accounts
        </h2>

        <form action="{{ route('accounts.transfer') }}" method="POST">
            @csrf

            {{-- From Account --}}
            <div class="form-group" style="margin-bottom:1rem">
                <label class="form-label">From Account <span class="required">*</span></label>
                <select name="from_account_id" id="fromAccountSelect" required class="form-control">
                    @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}">
                        {{ $acc->account_name }} (৳{{ number_format($acc->current_balance, 2) }})
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- To Account --}}
            <div class="form-group" style="margin-bottom:1rem">
                <label class="form-label">To Account <span class="required">*</span></label>
                <select name="to_account_id" id="toAccountSelect" required class="form-control">
                    @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}">
                        {{ $acc->account_name }} (৳{{ number_format($acc->current_balance, 2) }})
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Amount --}}
            <div class="form-group" style="margin-bottom:1rem">
                <label class="form-label">Amount <span class="required">*</span></label>
                <input type="number" name="amount" step="0.01" min="0.01" required
                    class="form-control" placeholder="0.00">
            </div>

            {{-- Description --}}
            <div class="form-group" style="margin-bottom:1rem">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-control"
                    placeholder="Optional note about this transfer">
            </div>

            {{-- Date --}}
            <div class="form-group" style="margin-bottom:1.5rem">
                <label class="form-label">Date <span class="required">*</span></label>
                <input type="date" name="date" required class="form-control"
                    value="{{ date('Y-m-d') }}">
            </div>

            <div style="display:flex;gap:0.75rem">
                <button type="submit" class="btn" style="flex:1;background:#6366f1;color:white;">
                    🔄 Transfer
                </button>
                <button type="button" onclick="closeTransferModal()"
                    class="btn btn-outline" style="flex:1">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ---- Deposit Modal ----
    function openDepositModal(accountId = null, accountName = null) {
        const modal = document.getElementById('depositModal');
        modal.style.display = 'flex';

        // Pre-select account if called from row button
        if (accountId) {
            const select = document.getElementById('depositAccountSelect');
            select.value = accountId;
            updateDepositAction(accountId);
        } else {
            // Default to first account
            const select = document.getElementById('depositAccountSelect');
            updateDepositAction(select.value);
        }
    }

    function closeDepositModal() {
        document.getElementById('depositModal').style.display = 'none';
    }

    function updateDepositAction(accountId) {
        const routes = @json($depositRoutes);
        document.getElementById('depositForm').action = routes[accountId];
    }

    // ---- Transfer Modal ----
    function openTransferModal(fromAccountId = null) {
        const modal = document.getElementById('transferModal');
        modal.style.display = 'flex';

        // Pre-select from account if called from row button
        if (fromAccountId) {
            document.getElementById('fromAccountSelect').value = fromAccountId;
        }
    }

    function closeTransferModal() {
        document.getElementById('transferModal').style.display = 'none';
    }

    // Close modals on backdrop click
    document.getElementById('depositModal').addEventListener('click', function(e) {
        if (e.target === this) closeDepositModal();
    });
    document.getElementById('transferModal').addEventListener('click', function(e) {
        if (e.target === this) closeTransferModal();
    });
</script>
@endpush