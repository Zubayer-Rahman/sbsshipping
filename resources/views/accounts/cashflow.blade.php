@extends('layouts.app')
@section('title','Cash Flow')
@section('page-title','Cash Flow Report')
@section('breadcrumb','Accounts / Cash Flow')

@section('content')
<style>
    .cashflow-container {
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

    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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

    .summary-card.credit {
        border-left-color: var(--success);
    }

    .summary-card.debit {
        border-left-color: var(--danger);
    }

    .summary-card.net {
        border-left-color: var(--primary);
    }

    .summary-label {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .summary-value {
        font-size: 1.875rem;
        font-weight: 700;
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

    .btn {
        padding: 0.625rem 1.5rem;
        border-radius: var(--radius-sm);
        font-weight: 500;
        text-decoration: none;
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
    }

    .btn-secondary {
        background: var(--text-muted);
        color: white;
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

    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
        color: var(--text-muted);
    }
</style>

<div class="cashflow-container">
    <div class="page-header">
        <h1 class="page-title">Cash Flow Report</h1>
        <a href="{{ route('accounts.index') }}" class="back-link">← Back to Accounts</a>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card credit">
            <p class="summary-label">Total Credits (Money In)</p>
            <p class="summary-value" style="color: var(--success);">৳{{ number_format($totalCredit, 2) }}</p>
        </div>
        <div class="summary-card debit">
            <p class="summary-label">Total Debits (Money Out)</p>
            <p class="summary-value" style="color: var(--danger);">৳{{ number_format($totalDebit, 2) }}</p>
        </div>
        <div class="summary-card net">
            <p class="summary-label">Net Cash Flow</p>
            <p class="summary-value" style="color: {{ ($totalCredit - $totalDebit) >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                ৳{{ number_format($totalCredit - $totalDebit, 2) }}
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET">
            <div class="filter-grid">
                <div class="form-group">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Account</label>
                    <select name="account_id" class="form-control">
                        <option value="">All Accounts</option>
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                            {{ $acc->account_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>Credit</option>
                        <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>Debit</option>
                    </select>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('accounts.cashflow') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="table-card">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Account</th>
                        <th>Reference</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right">Balance After</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('accounts.show', $transaction->account) }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                                {{ $transaction->account->account_name }}
                            </a>
                        </td>
                        <td>
                            <span style="font-family: monospace; font-size: 0.875rem; color: var(--text-muted);">
                                {{ $transaction->reference_number }}
                            </span>
                        </td>
                        <td>
                            @if($transaction->transaction_type == 'credit')
                            <span class="badge badge-success">Credit ↑</span>
                            @else
                            <span class="badge badge-danger">Debit ↓</span>
                            @endif
                        </td>
                        <td>{{ $transaction->description }}</td>
                        <td class="text-right {{ $transaction->transaction_type == 'credit' ? 'transaction-credit' : 'transaction-debit' }}">
                            {{ $transaction->transaction_type == 'credit' ? '+' : '-' }}৳{{ number_format($transaction->amount, 2) }}
                        </td>
                        <td class="text-right" style="font-weight: 600;">
                            ৳{{ number_format($transaction->balance_after, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            No transactions found for the selected filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 1.5rem;">
        {{ $transactions->links() }}
    </div>
</div>
@endsection