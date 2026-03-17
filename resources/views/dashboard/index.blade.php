@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Overview')

@section('content')

{{-- ── STAT BOXES ────────────────────────────────────────────────────────── --}}
<div class="stats-grid">

    <div class="stat-card blue">
        <div class="stat-icon"><i class="bi bi-receipt"></i></div>
        <div class="stat-value">৳ {{ number_format($totalBill, 0) }}</div>
        <div class="stat-label">Total Bill</div>
        <div class="stat-change up"><i class="bi bi-arrow-up-right"></i> All invoiced revenue</div>
    </div>

    <div class="stat-card green">
        <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
        <div class="stat-value">৳ {{ number_format($totalExpense, 0) }}</div>
        <div class="stat-label">Total Expense</div>
        <div class="stat-change down"><i class="bi bi-arrow-down-right"></i> Operational costs</div>
    </div>

    <div class="stat-card orange">
        <div class="stat-icon"><i class="bi bi-exclamation-circle"></i></div>
        <div class="stat-value">৳ {{ number_format($totalDues, 0) }}</div>
        <div class="stat-label">Total Dues</div>
        <div class="stat-change {{ $totalDues > 0 ? 'down' : 'up' }}">
            <i class="bi bi-{{ $totalDues > 0 ? 'arrow-up-right' : 'check-circle' }}"></i>
            {{ $totalDues > 0 ? 'Outstanding balance' : 'No outstanding dues' }}
        </div>
    </div>

    <div class="stat-card red">
        <div class="stat-icon"><i class="bi bi-briefcase"></i></div>
        <div class="stat-value">{{ number_format($totalJobs) }}</div>
        <div class="stat-label">Total Jobs</div>
        <div class="stat-change up"><i class="bi bi-arrow-up-right"></i> All time jobs</div>
    </div>

</div>

{{-- ── ROW 1: Line Chart + Pie Chart ───────────────────────────────────────── --}}
<div class="charts-grid" style="margin-bottom:18px">
    {{-- Monthly Jobs Line Chart --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-graph-up" style="color:var(--primary);margin-right:6px"></i>Jobs Over Time</span>
            <span style="font-size:12px;color:var(--text-muted)">Last 12 months</span>
        </div>
        <div class="card-body">
            <canvas id="lineChart" height="90"></canvas>
        </div>
    </div>

    {{-- Job Status Pie --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-pie-chart" style="color:var(--accent);margin-right:6px"></i>Job Status</span>
        </div>
        <div class="card-body" style="display:flex;align-items:center;justify-content:center">
            <canvas id="statusPie" height="160" style="max-height:300px"></canvas>
        </div>
    </div>

</div>

{{-- ── ROW 2: Bar Chart + Cargo Pie ────────────────────────────────────────── --}}
<div class="charts-grid" style="margin-bottom:28px">

    {{-- Monthly Revenue Bar Chart --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-bar-chart" style="color:var(--success);margin-right:6px"></i>Monthly Revenue</span>
            <span style="font-size:12px;color:var(--text-muted)">BDT (৳)</span>
        </div>
        <div class="card-body">
            <canvas id="barChart" height="90"></canvas>
        </div>
    </div>

    {{-- Cargo Type Pie --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-boxes" style="color:var(--warning);margin-right:6px"></i>Cargo Types</span>
        </div>
        <div class="card-body" style="display:flex;align-items:center;justify-content:center">
            <canvas id="cargoPie" height="160" style="max-height:300px"></canvas>
        </div>
    </div>

</div>

{{-- ── RECENT JOBS TABLE ────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header" style="padding-bottom:16px">
        <span class="card-title"><i class="bi bi-clock-history" style="color:var(--primary);margin-right:6px"></i>Recent Jobs</span>
        <a href="{{ route('jobs.list') }}" class="btn btn-outline btn-sm">View All <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Client</th>
                    <th>Origin → Destination</th>
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

@push('scripts')
<script>
    // ── Prepare data from PHP ────────────────────────────────────────────────────
    const monthlyJobsRaw = @json($monthlyJobs);
    const monthlyRevRaw = @json($monthlyRevenue);
    const statusRaw = @json($statusData);
    const cargoRaw = @json($cargoData);

    // Build labels for last 12 months
    function buildMonthLabels() {
        const labels = [];
        for (let i = 11; i >= 0; i--) {
            const d = new Date();
            d.setMonth(d.getMonth() - i);
            labels.push(d.toLocaleString('default', {
                month: 'short',
                year: '2-digit'
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
    const jobData = mapToMonthly(monthlyJobsRaw, 'count');
    const revData = mapToMonthly(monthlyRevRaw, 'total');

    const BLUE = '#1a56db';
    const ACCENT = '#06b6d4';
    const GREEN = '#10b981';
    const ORANGE = '#f59e0b';
    const RED = '#ef4444';
    const PURPLE = '#8b5cf6';

    // ── 1. Line Chart ────────────────────────────────────────────────────────────
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Jobs',
                data: jobData,
                borderColor: BLUE,
                backgroundColor: 'rgba(26,86,219,.08)',
                fill: true,
                tension: .4,
                pointBackgroundColor: BLUE,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: '#f1f5f9'
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

    // ── 2. Bar Chart ─────────────────────────────────────────────────────────────
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Revenue (৳)',
                data: revData,
                backgroundColor: labels.map((_, i) => `rgba(16,185,129,${0.5 + (i/11)*0.5})`),
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
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

    // ── 3. Status Pie ─────────────────────────────────────────────────────────────
    const statusColors = {
        pending: ORANGE,
        'in-transit': BLUE,
        delivered: GREEN,
        cancelled: RED
    };
    new Chart(document.getElementById('statusPie'), {
        type: 'doughnut',
        data: {
            labels: statusRaw.map(s => s.status),
            datasets: [{
                data: statusRaw.map(s => s.count),
                backgroundColor: statusRaw.map(s => statusColors[s.status] || PURPLE),
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 14,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // ── 4. Cargo Pie ─────────────────────────────────────────────────────────────
    const palette = [BLUE, ACCENT, GREEN, ORANGE, RED, PURPLE, '#ec4899', '#14b8a6'];
    new Chart(document.getElementById('cargoPie'), {
        type: 'doughnut',
        data: {
            labels: cargoRaw.length ? cargoRaw.map(c => c.cargo_type || 'Unknown') : ['No Data'],
            datasets: [{
                data: cargoRaw.length ? cargoRaw.map(c => c.count) : [1],
                backgroundColor: cargoRaw.length ? cargoRaw.map((_, i) => palette[i % palette.length]) : ['#e2e8f0'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 14,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
</script>
@endpush