@extends('layouts.app')
@section('title', $account->account_name)
@section('page-title', $account->account_name)
@section('breadcrumb','Accounts / ' . $account->account_name)

@section('content')
<style>
    .account-show-container {
        padding: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        font-family: 'Inter', sans-serif;
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
    }

    .back-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }

    .account-info-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 2rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 1.5rem;
    }

    .info-item-label {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .info-item-value {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 1rem;
    }

    .balance-display {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--border);
    }

    .transactions-card {
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

    .data-table tbody tr:hover {
        background: var(--primary-light);
    }

    .transaction-credit {
        color: var(--success);
        font-weight: 700;
    }

    .transaction-debit {
        color: var(--danger);
        font-weight: 700;
    }

    .text-right {
        text-align: right;
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
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

    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
        color: var(--text-muted);
    }

    .status-toggle-form {
        display: inline;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-sm);
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }
</style>

<div class="account-show-container">
    <div class="page-header">
        <h1 class="page-title">{{ $account->account_name }}</h1>
        <a href="{{ route('accounts.index') }}" class="back-link">← Back to Accounts</a>
    </div>

    <!-- Account Information -->
    <div class="account-info-card">
        <div class="info-grid">

            <div>
                <p class="info-item-label">Current Balance</p>
                <p class="balance-display">৳{{ number_format($account->current_balance, 2) }}</p>
            </div>

            <div>
                <p class="info-item-label">Opening Balance</p>
                <p class="info-item-value">৳{{ number_format($account->opening_balance, 2) }}</p>
            </div>

            @if($account->bank_name)
            <div>
                <p class="info-item-label">Bank Name</p>
                <p class="info-item-value">{{ $account->bank_name }}</p>
            </div>
            @endif

            @if($account->account_number)
            <div>
                <p class="info-item-label">Account Number</p>
                <p class="info-item-value">{{ $account->account_number }}</p>
            </div>
            @endif

            @if($account->branch)
            <div>
                <p class="info-item-label">Branch</p>
                <p class="info-item-value">{{ $account->branch }}</p>
            </div>
            @endif

            <div>
                <p class="info-item-label">Status</p>
                <p class="info-item-value">
                    <span class="badge {{ $account->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $account->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>
                <form action="{{ route('accounts.toggle', $account) }}" method="POST" class="status-toggle-form" style="margin-top: 0.5rem;">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $account->is_active ? 'btn-danger' : 'btn-success' }}">
                        {{ $account->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Credit / Debit Summary ── --}}
        @php
        $totalCredit = $account->transactions->where('transaction_type', 'credit')->sum('amount');
        $totalDebit = $account->transactions->where('transaction_type', 'debit')->sum('amount');
        @endphp

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:20px">

            <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:10px;padding:18px 22px">
                <p style="font-size:12px;font-weight:700;color:#065f46;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">
                    <i class="bi bi-arrow-down-circle-fill" style="margin-right:5px"></i>Total Credited
                </p>
                <p style="font-size:24px;font-weight:800;color:#10b981;margin:0">
                    +৳{{ number_format($totalCredit, 2) }}
                </p>
                <p style="font-size:12px;color:#065f46;margin-top:4px">
                    {{ $account->transactions->where('transaction_type', 'credit')->count() }} transactions
                </p>
            </div>

            <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:10px;padding:18px 22px">
                <p style="font-size:12px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">
                    <i class="bi bi-arrow-up-circle-fill" style="margin-right:5px"></i>Total Debited
                </p>
                <p style="font-size:24px;font-weight:800;color:#ef4444;margin:0">
                    -৳{{ number_format($totalDebit, 2) }}
                </p>
                <p style="font-size:12px;color:#991b1b;margin-top:4px">
                    {{ $account->transactions->where('transaction_type', 'debit')->count() }} transactions
                </p>
            </div>

        </div>

        @if($account->description)
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
            <p class="info-item-label">Description</p>
            <p class="info-item-value">{{ $account->description }}</p>
        </div>
        @endif
    </div>

    <!-- Transaction History -->
    <h2 class="section-title">Transaction History</h2>

    <div class="transactions-card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right">Balance After</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($account->transactions->sortByDesc('transaction_date') as $transaction)
                    <tr>
                        <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                        <td>
                            <span style="font-family: monospace; font-size: 0.875rem; color: var(--text-muted);">
                                {{ $transaction->reference_number }}
                            </span>
                        </td>
                        <td>
                            @if($transaction->transaction_type == 'credit')
                            <span class="badge badge-success">Credit</span>
                            @else
                            <span class="badge badge-danger">Debit</span>
                            @endif
                        </td>
                        <td>{{ $transaction->description }}</td>
                        <td class="text-right {{ $transaction->transaction_type == 'credit' ? 'transaction-credit' : 'transaction-debit' }}">
                            {{ $transaction->transaction_type == 'credit' ? '+' : '-' }}৳{{ number_format($transaction->amount, 2) }}
                        </td>
                        <td class="text-right" style="font-weight: 600;">
                            ৳{{ number_format($transaction->balance_after, 2) }}
                        </td>
                        <td>{{ $transaction->creator->name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            No transactions recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection