@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Overview')

@section('content')

{{-- ── HERO STATS WITH SPARKLINES ─────────────────────────────────────── --}}
<div class="hero-stats">

    <div class="hero-card hero-revenue">
        <div class="hero-icon-wrap">
            <i class="bi bi-cash-coin"></i>
        </div>
        <div class="hero-content">
            <div class="hero-label">Total Revenue</div>
            <div class="hero-value">৳ {{ number_format($totalBill, 0) }}</div>
            <div class="hero-trend up">
                <i class="bi bi-arrow-up-right"></i>
                <span>All time earnings</span>
            </div>
        </div>
        <div class="hero-spark">
            <canvas id="sparkRevenue"></canvas>
        </div>
    </div>

    <div class="hero-card hero-expense">
        <div class="hero-icon-wrap">
            <i class="bi bi-credit-card-2-back"></i>
        </div>
        <div class="hero-content">
            <div class="hero-label">Total Expenses</div>
            <div class="hero-value">৳ {{ number_format($totalExpense, 0) }}</div>
            <div class="hero-trend down">
                <i class="bi bi-arrow-down-right"></i>
                <span>Operational costs</span>
            </div>
        </div>
        <div class="hero-spark">
            <canvas id="sparkExpense"></canvas>
        </div>
    </div>

    <div class="hero-card hero-profit">
        <div class="hero-icon-wrap">
            <i class="bi bi-graph-up-arrow"></i>
        </div>
        <div class="hero-content">
            <div class="hero-label">Net Profit</div>
            <div class="hero-value">৳ {{ number_format($totalBill - $totalExpense, 0) }}</div>
            <div class="hero-trend up">
                <i class="bi bi-circle-fill"></i>
                <span>{{ $totalBill > 0 ? number_format((($totalBill - $totalExpense) / $totalBill) * 100, 1) : 0 }}% margin</span>
            </div>
        </div>
        <div class="hero-spark">
            <canvas id="sparkProfit"></canvas>
        </div>
    </div>

    <div class="hero-card hero-pending">
        <div class="hero-icon-wrap">
            <i class="bi bi-hourglass-split"></i>
        </div>
        <div class="hero-content">
            <div class="hero-label">Outstanding Dues</div>
            <div class="hero-value">৳ {{ number_format($totalDues, 0) }}</div>
            <div class="hero-trend {{ $totalDues > 0 ? 'down' : 'up' }}">
                <i class="bi bi-{{ $totalDues > 0 ? 'exclamation-circle' : 'check-circle' }}"></i>
                <span>{{ $totalDues > 0 ? 'Needs collection' : 'All clear!' }}</span>
            </div>
        </div>
        <div class="hero-spark">
            <canvas id="sparkDues"></canvas>
        </div>
    </div>

</div>

{{-- ── ROW 1: Cash Flow + Smart Alerts ─────────────────────────────────── --}}
<div class="dash-row-1">

    {{-- Cash Flow Chart --}}
    <div class="card chart-card">
        <div class="card-header">
            <div>
                <span class="card-title">
                    <i class="bi bi-activity" style="color:var(--primary)"></i> Cash Flow
                </span>
                <div style="font-size:12px;color:var(--text-muted);margin-top:2px">
                    Revenue vs Expenses over last 12 months
                </div>
            </div>
            <div class="chart-legend">
                <span class="legend-item"><span class="legend-dot" style="background:#10b981"></span> Revenue</span>
                <span class="legend-item"><span class="legend-dot" style="background:#ef4444"></span> Expenses</span>
            </div>
        </div>
        <div class="card-body">
            <canvas id="cashFlowChart" height="100"></canvas>
        </div>
    </div>

    {{-- Smart Alerts --}}
    <div class="card alerts-card">
        <div class="card-header">
            <span class="card-title">
                <i class="bi bi-bell" style="color:var(--warning)"></i> Smart Alerts
            </span>
            <span class="alert-badge">{{ $alertCount ?? 3 }}</span>
        </div>
        <div class="alerts-list">
            @if($totalDues > 0)
            <div class="alert-item alert-warning">
                <div class="alert-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="alert-content">
                    <div class="alert-title">Outstanding Payments</div>
                    <div class="alert-desc">৳{{ number_format($totalDues, 0) }} pending collection</div>
                </div>
            </div>
            @endif

            @if(isset($overdueBills) && $overdueBills > 0)
            <div class="alert-item alert-danger">
                <div class="alert-icon"><i class="bi bi-clock-history"></i></div>
                <div class="alert-content">
                    <div class="alert-title">{{ $overdueBills }} Overdue Bills</div>
                    <div class="alert-desc">Past due date — follow up needed</div>
                </div>
            </div>
            @endif

            @if(isset($lowAccount) && $lowAccount)
            <div class="alert-item alert-info">
                <div class="alert-icon"><i class="bi bi-wallet2"></i></div>
                <div class="alert-content">
                    <div class="alert-title">Low Account Balance</div>
                    <div class="alert-desc">{{ $lowAccount->account_name }} below ৳5,000</div>
                </div>
            </div>
            @endif

            <div class="alert-item alert-success">
                <div class="alert-icon"><i class="bi bi-check2-circle"></i></div>
                <div class="alert-content">
                    <div class="alert-title">{{ $totalJobs }} Jobs Active</div>
                    <div class="alert-desc">Business running smoothly</div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── ROW 2: Job Funnel + Account Balances + Top Clients ─────────────── --}}
<div class="dash-row-2">

    {{-- Job Status Funnel --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <i class="bi bi-funnel" style="color:var(--accent)"></i> Job Pipeline
            </span>
        </div>
        <div class="card-body funnel-body">
            @php
            $statusMap = collect($statusData)->keyBy('status');
            $stages = [
            ['key' => 'pending', 'label' => 'Pending', 'color' => '#f59e0b', 'icon' => 'hourglass'],
            ['key' => 'in-transit', 'label' => 'In Transit', 'color' => '#1a56db', 'icon' => 'truck'],
            ['key' => 'delivered', 'label' => 'Delivered', 'color' => '#10b981', 'icon' => 'check2-all'],
            ];
            $maxCount = max(collect($statusData)->pluck('count')->toArray() ?: [1]);
            @endphp

            @foreach($stages as $stage)
            @php
            $count = $statusMap[$stage['key']]->count ?? 0;
            $width = $maxCount > 0 ? ($count / $maxCount * 100) : 0;
            @endphp
            <div class="funnel-row">
                <div class="funnel-label">
                    <i class="bi bi-{{ $stage['icon'] }}" style="color:{{ $stage['color'] }}"></i>
                    <span>{{ $stage['label'] }}</span>
                </div>
                <div class="funnel-bar-wrap">
                    <div class="funnel-bar" style="width:{{ $width }}%;background:{{ $stage['color'] }}"></div>
                </div>
                <div class="funnel-count">{{ $count }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Account Balances --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <i class="bi bi-bank" style="color:var(--success)"></i> Account Balances
            </span>
            <a href="{{ route('accounts.index') }}" class="link-small">View all <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="card-body accounts-body">
            @php
            $accounts = \App\Models\PaymentAccount::where('is_active', true)->take(4)->get();
            $totalBalance = $accounts->sum('current_balance');
            @endphp

            <div class="total-balance-wrap">
                <div class="total-balance-label">Total Balance</div>
                <div class="total-balance-value">৳ {{ number_format($totalBalance, 0) }}</div>
            </div>

            <div class="accounts-list">
                @forelse($accounts as $acc)
                <div class="account-row">
                    <div class="account-icon-mini" style="background:{{ ['#dbeafe', '#dcfce7', '#fef3c7', '#fee2e2'][$loop->index % 4] }}">
                        <i class="bi bi-{{ ['bank', 'cash-coin', 'phone', 'credit-card'][$loop->index % 4] }}"
                            style="color:{{ ['#1e40af', '#15803d', '#a16207', '#b91c1c'][$loop->index % 4] }}"></i>
                    </div>
                    <div class="account-info-mini">
                        <div class="account-name-mini">{{ $acc->account_name }}</div>
                        <div class="account-type-mini">{{ ucfirst(str_replace('_', ' ', $acc->account_type)) }}</div>
                    </div>
                    <div class="account-balance-mini">৳ {{ number_format($acc->current_balance, 0) }}</div>
                </div>
                @empty
                <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px">
                    No accounts yet
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Top Clients --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">
                <i class="bi bi-trophy" style="color:var(--warning)"></i> Top Clients
            </span>
        </div>
        <div class="card-body">
            @php
            $topClients = \App\Models\Job::selectRaw('client_name, COUNT(*) as job_count, SUM(cost_amount) as total_value')
            ->whereNotNull('client_name')
            ->groupBy('client_name')
            ->orderByDesc('total_value')
            ->take(5)
            ->get();
            @endphp

            @forelse($topClients as $idx => $client)
            <div class="client-row">
                <div class="client-rank rank-{{ $idx + 1 }}">{{ $idx + 1 }}</div>
                <div class="client-info">
                    <div class="client-name">{{ Str::limit($client->client_name, 25) }}</div>
                    <div class="client-meta">{{ $client->job_count }} jobs</div>
                </div>
                <div class="client-value">৳ {{ number_format($client->total_value, 0) }}</div>
            </div>
            @empty
            <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px">
                No client data yet
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── ROW 3: Quick Actions ───────────────────────────────────────────── --}}
<div class="quick-actions">
    <a href="{{ route('jobs.create') }}" class="quick-action">
        <div class="qa-icon" style="background:#dbeafe;color:#1e40af">
            <i class="bi bi-plus-circle"></i>
        </div>
        <div>
            <div class="qa-title">New Job</div>
            <div class="qa-sub">Create shipping job</div>
        </div>
    </a>
    <a href="{{ route('bills.create') }}" class="quick-action">
        <div class="qa-icon" style="background:#dcfce7;color:#15803d">
            <i class="bi bi-receipt"></i>
        </div>
        <div>
            <div class="qa-title">New Bill</div>
            <div class="qa-sub">Invoice a client</div>
        </div>
    </a>
    <a href="{{ route('expenses.create') }}" class="quick-action">
        <div class="qa-icon" style="background:#fef3c7;color:#a16207">
            <i class="bi bi-cash-stack"></i>
        </div>
        <div>
            <div class="qa-title">Add Expense</div>
            <div class="qa-sub">Record spending</div>
        </div>
    </a>
    <a href="{{ route('ious.create') }}" class="quick-action">
        <div class="qa-icon" style="background:#fee2e2;color:#b91c1c">
            <i class="bi bi-file-earmark-text"></i>
        </div>
        <div>
            <div class="qa-title">Create IOU</div>
            <div class="qa-sub">Track money owed</div>
        </div>
    </a>
    <a href="{{ route('accounts.cashflow') }}" class="quick-action">
        <div class="qa-icon" style="background:#e0e7ff;color:#4338ca">
            <i class="bi bi-arrow-left-right"></i>
        </div>
        <div>
            <div class="qa-title">Cash Flow</div>
            <div class="qa-sub">View transactions</div>
        </div>
    </a>
</div>

{{-- ── RECENT JOBS ────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="bi bi-clock-history" style="color:var(--primary)"></i> Recent Jobs
        </span>
        <a href="{{ route('jobs.list') }}" class="btn btn-outline btn-sm">View All <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Client</th>
                    <th>Route</th>
                    <th>Cargo</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentJobs as $job)
                <tr>
                    <td><span style="font-weight:700;color:var(--primary)">{{ $job->job_id }}</span></td>
                    <td>{{ $job->client_name ?? '—' }}</td>
                    <td>{{ $job->origin ?? '—' }} → {{ $job->destination ?? '—' }}</td>
                    <td>{{ $job->cargo_type ?? '—' }}</td>
                    <td>৳ {{ number_format($job->cost_amount ?? 0, 0) }}</td>
                    <td>
                        <span class="badge badge-{{ str_replace(' ', '-', $job->status) }}">
                            {{ ucfirst($job->status) }}
                        </span>
                    </td>
                    <td style="color:var(--text-muted)">{{ $job->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:30px;color:var(--text-muted)">
                        <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px;opacity:.4"></i>
                        No jobs yet. <a href="{{ route('jobs.create') }}" style="color:var(--primary)">Create your first job →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* ─── HERO STATS ─── */
    .hero-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 24px;
    }

    .hero-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 22px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: transform .2s, box-shadow .2s;
        display: flex;
        flex-direction: column;
        min-height: 160px;
    }

    .hero-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .hero-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }

    .hero-revenue::before {
        background: linear-gradient(90deg, #1a56db, #06b6d4);
    }

    .hero-expense::before {
        background: linear-gradient(90deg, #ef4444, #f59e0b);
    }

    .hero-profit::before {
        background: linear-gradient(90deg, #10b981, #06b6d4);
    }

    .hero-pending::before {
        background: linear-gradient(90deg, #f59e0b, #ef4444);
    }

    .hero-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 12px;
    }

    .hero-revenue .hero-icon-wrap {
        background: rgba(26, 86, 219, .1);
        color: #1a56db;
    }

    .hero-expense .hero-icon-wrap {
        background: rgba(239, 68, 68, .1);
        color: #ef4444;
    }

    .hero-profit .hero-icon-wrap {
        background: rgba(16, 185, 129, .1);
        color: #10b981;
    }

    .hero-pending .hero-icon-wrap {
        background: rgba(245, 158, 11, .1);
        color: #f59e0b;
    }

    .hero-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 4px;
    }

    .hero-value {
        font-size: 26px;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.1;
        margin-bottom: 6px;
    }

    .hero-trend {
        font-size: 11px;
        display: flex;
        align-items: center;
        gap: 4px;
        font-weight: 600;
    }

    .hero-trend.up {
        color: #10b981;
    }

    .hero-trend.down {
        color: #ef4444;
    }

    .hero-spark {
        position: absolute;
        bottom: 12px;
        right: 12px;
        width: 80px;
        height: 30px;
        opacity: .7;
    }

    /* ─── DASH ROW 1 ─── */
    .dash-row-1 {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 18px;
        margin-bottom: 18px;
    }

    .chart-legend {
        display: flex;
        gap: 14px;
    }

    .legend-item {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    /* ─── ALERTS ─── */
    .alerts-card {
        display: flex;
        flex-direction: column;
    }

    .alert-badge {
        background: var(--danger);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 12px;
    }

    .alerts-list {
        padding: 8px;
        overflow-y: auto;
        flex: 1;
    }

    .alert-item {
        display: flex;
        gap: 12px;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 8px;
        align-items: flex-start;
        transition: background .2s;
    }

    .alert-item:hover {
        transform: translateX(2px);
    }

    .alert-warning {
        background: rgba(245, 158, 11, .08);
        border-left: 3px solid #f59e0b;
    }

    .alert-danger {
        background: rgba(239, 68, 68, .08);
        border-left: 3px solid #ef4444;
    }

    .alert-info {
        background: rgba(26, 86, 219, .08);
        border-left: 3px solid #1a56db;
    }

    .alert-success {
        background: rgba(16, 185, 129, .08);
        border-left: 3px solid #10b981;
    }

    .alert-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 15px;
    }

    .alert-warning .alert-icon {
        color: #f59e0b;
    }

    .alert-danger .alert-icon {
        color: #ef4444;
    }

    .alert-info .alert-icon {
        color: #1a56db;
    }

    .alert-success .alert-icon {
        color: #10b981;
    }

    .alert-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .alert-desc {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    /* ─── DASH ROW 2 ─── */
    .dash-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 18px;
        margin-bottom: 18px;
    }

    /* ─── FUNNEL ─── */
    .funnel-body {
        padding: 24px 18px !important;
    }

    .funnel-row {
        display: grid;
        grid-template-columns: 120px 1fr 50px;
        gap: 12px;
        align-items: center;
        margin-bottom: 16px;
    }

    .funnel-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .funnel-bar-wrap {
        height: 28px;
        background: var(--body-bg);
        border-radius: 8px;
        overflow: hidden;
    }

    .funnel-bar {
        height: 100%;
        border-radius: 8px;
        transition: width .8s ease;
        position: relative;
        overflow: hidden;
    }

    .funnel-bar::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .3));
    }

    .funnel-count {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-primary);
        text-align: right;
    }

    /* ─── ACCOUNTS ─── */
    .accounts-body {
        padding: 16px !important;
    }

    .total-balance-wrap {
        text-align: center;
        padding: 14px;
        background: linear-gradient(135deg, var(--primary-light), #fff);
        border-radius: 10px;
        margin-bottom: 14px;
        border: 1px solid var(--primary-glow);
    }

    .total-balance-label {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .total-balance-value {
        font-size: 22px;
        font-weight: 800;
        color: var(--primary);
        margin-top: 4px;
    }

    .account-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 8px;
        border-bottom: 1px solid var(--border);
    }

    .account-row:last-child {
        border-bottom: none;
    }

    .account-icon-mini {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .account-info-mini {
        flex: 1;
        min-width: 0;
    }

    .account-name-mini {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .account-type-mini {
        font-size: 11px;
        color: var(--text-muted);
    }

    .account-balance-mini {
        font-size: 13px;
        font-weight: 700;
        color: var(--primary);
    }

    /* ─── CLIENTS ─── */
    .client-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 8px;
        border-bottom: 1px solid var(--border);
    }

    .client-row:last-child {
        border-bottom: none;
    }

    .client-rank {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
        color: #fff;
        flex-shrink: 0;
    }

    .rank-1 {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
    }

    .rank-2 {
        background: linear-gradient(135deg, #94a3b8, #64748b);
    }

    .rank-3 {
        background: linear-gradient(135deg, #d97706, #92400e);
    }

    .rank-4,
    .rank-5 {
        background: var(--body-bg);
        color: var(--text-muted);
    }

    .client-info {
        flex: 1;
        min-width: 0;
    }

    .client-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .client-meta {
        font-size: 11px;
        color: var(--text-muted);
    }

    .client-value {
        font-size: 13px;
        font-weight: 700;
        color: var(--success);
    }

    /* ─── QUICK ACTIONS ─── */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }

    .quick-action {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        box-shadow: var(--shadow-sm);
        transition: all .2s;
        border: 1px solid var(--border);
    }

    .quick-action:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }

    .qa-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .qa-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .qa-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .link-small {
        font-size: 12px;
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
    }

    .link-small:hover {
        text-decoration: underline;
    }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 1200px) {
        .hero-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .dash-row-1 {
            grid-template-columns: 1fr;
        }

        .dash-row-2 {
            grid-template-columns: 1fr 1fr;
        }

        .quick-actions {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .hero-stats {
            grid-template-columns: 1fr;
        }

        .dash-row-2 {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // ── Data prep ──
    const monthlyJobsRaw = @json($monthlyJobs);
    const monthlyRevRaw = @json($monthlyRevenue);
    const monthlyExpRaw = @json($monthlyExpense ?? []);
    const statusRaw = @json($statusData);

    function buildMonthLabels() {
        const labels = [];
        for (let i = 11; i >= 0; i--) {
            const d = new Date();
            d.setMonth(d.getMonth() - i);
            labels.push(d.toLocaleString('default', {
                month: 'short'
            }));
        }
        return labels;
    }

    function mapToMonthly(data, valueKey) {
        const now = new Date();
        return Array.from({
            length: 12
        }, (_, i) => {
            const d = new Date();
            d.setMonth(now.getMonth() - (11 - i));
            const m = d.getMonth() + 1,
                y = d.getFullYear();
            const match = data.find(r => parseInt(r.month) === m && parseInt(r.year) === y);
            return match ? parseFloat(match[valueKey]) : 0;
        });
    }

    const labels = buildMonthLabels();
    const revData = mapToMonthly(monthlyRevRaw, 'total');
    const expData = mapToMonthly(monthlyExpRaw, 'total');
    const profitData = revData.map((r, i) => r - (expData[i] || 0));
    const duesData = revData.map(r => r * 0.15); // Example

    // ── Sparkline helper ──
    function makeSpark(canvasId, data, color) {
        new Chart(document.getElementById(canvasId), {
            type: 'line',
            data: {
                labels: data.map((_, i) => i),
                datasets: [{
                    data: data,
                    borderColor: color,
                    backgroundColor: color + '22',
                    fill: true,
                    tension: .4,
                    pointRadius: 0,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                },
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        display: false
                    }
                },
                elements: {
                    line: {
                        borderWidth: 2
                    }
                }
            }
        });
    }

    makeSpark('sparkRevenue', revData, '#1a56db');
    makeSpark('sparkExpense', expData, '#ef4444');
    makeSpark('sparkProfit', profitData, '#10b981');
    makeSpark('sparkDues', duesData, '#f59e0b');

    // ── Cash Flow Chart ──
    new Chart(document.getElementById('cashFlowChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                    label: 'Revenue',
                    data: revData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,.1)',
                    fill: true,
                    tension: .4,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981',
                    borderWidth: 3,
                },
                {
                    label: 'Expenses',
                    data: expData,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,.1)',
                    fill: true,
                    tension: .4,
                    pointRadius: 4,
                    pointBackgroundColor: '#ef4444',
                    borderWidth: 3,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: '#0f1f4b',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 8,
                }
            },
            interaction: {
                mode: 'nearest',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        callback: v => '৳ ' + (v >= 1000 ? (v / 1000) + 'k' : v)
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endpush