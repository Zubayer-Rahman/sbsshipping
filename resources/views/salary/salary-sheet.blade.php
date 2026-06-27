@extends('layouts.app')

@section('title', 'Salary Sheet')
@section('page-title', 'Salary Sheet')
@section('breadcrumb', 'Salary / Sheet')

@push('styles')
<style>
    .sal-table th,
    .sal-table td {
        padding: 11px 14px;
        font-size: 13px;
        white-space: nowrap;
    }

    .sal-table th {
        background: var(--body-bg);
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--text-muted);
    }

    .sal-table tbody tr:hover {
        background: #f8faff;
    }

    .sal-table tbody tr {
        border-bottom: 1px solid var(--border);
    }

    .net-payable {
        font-weight: 800;
        color: var(--primary);
        font-size: 14px;
    }

    .saving-dot::after {
        content: ' ●';
        color: var(--warning);
        font-size: 8px;
        animation: blink 1s infinite;
    }

    @keyframes blink {
        50% {
            opacity: 0;
        }
    }

    .month-label {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-primary);
    }

    .totals-row td {
        font-weight: 700;
        background: var(--primary-light);
        color: var(--primary);
    }

    .inline-input {
        border: 1.5px solid var(--border);
        border-radius: 6px;
        padding: 5px 8px;
        font-family: inherit;
        font-size: 13px;
        width: 120px;
        outline: none;
        transition: border-color .2s;
    }

    .inline-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .remarks-input {
        width: 180px;
    }
</style>
@endpush

@section('content')

{{-- Header controls --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
        @php
        $prevMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
        $nextMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
        @endphp
        <a href="{{ route('salary.sheet', ['year'=>$prevMonth->year,'month'=>$prevMonth->month]) }}" class="btn btn-outline btn-sm"><i class="bi bi-chevron-left"></i></a>
        <span class="month-label">{{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</span>
        <a href="{{ route('salary.sheet', ['year'=>$nextMonth->year,'month'=>$nextMonth->month]) }}" class="btn btn-outline btn-sm"><i class="bi bi-chevron-right"></i></a>

        <form method="GET" action="{{ route('salary.sheet') }}" style="display:flex;gap:8px;align-items:center">
            <select name="month" class="form-select" style="width:120px">
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ $m==$month?'selected':'' }}>{{ \Carbon\Carbon::createFromDate($year,$m,1)->format('F') }}</option>
                    @endfor
            </select>
            <select name="year" class="form-select" style="width:90px">
                @for($y=now()->year-2;$y<=now()->year+1;$y++)
                    <option value="{{ $y }}" {{ $y==$year?'selected':'' }}>{{ $y }}</option>
                    @endfor
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Go</button>
        </form>

        <div style="margin-left:auto;display:flex;gap:10px">
            <a href="{{ route('salary.attendance', ['year'=>$year,'month'=>$month]) }}" class="btn btn-outline btn-sm">
                <i class="bi bi-calendar-check"></i> Attendance Sheet
            </a>
            <a href="{{ route('salary.staff.index') }}" class="btn btn-outline btn-sm">
                <i class="bi bi-people"></i> Manage Staff
            </a>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="padding-bottom:14px">
        <h2 class="card-title">
            Salary Sheet — {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
        </h2>
        <span style="font-size:12px;color:var(--text-muted)">
            <i class="bi bi-info-circle"></i> Edit Advance/Cut or Remarks — net payable updates instantly
        </span>
    </div>
    <div class="table-wrapper" style="overflow-x:auto">
        <table class="sal-table" style="width:100%;border-collapse:collapse">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th style="text-align:right">Per Day Salary</th>
                    <th style="text-align:center">Working Days</th>
                    <th style="text-align:center">Absent Days</th>
                    <th style="text-align:right">Absent Deduction</th>
                    <th style="text-align:right">Gross Salary</th>
                    <th style="text-align:right">Advance / Cut (৳)</th>
                    <th style="text-align:right">Net Payable</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                @php $s = $row['staff']; @endphp
                <tr id="row-{{ $s->id }}">
                    <td>{{ $row['sl'] }}</td>
                    <td style="font-weight:600">{{ $s->name }}</td>
                    <td style="color:var(--text-muted)">{{ $s->position }}</td>
                    <td style="text-align:right">৳ {{ number_format($row['per_day_salary'], 2) }}</td>
                    <td style="text-align:center">{{ $row['working_days'] }}</td>
                    <td style="text-align:center" id="absent-days-{{ $s->id }}">{{ $row['absent_days'] }}</td>
                    <td style="text-align:right;color:var(--danger)" id="absent-ded-{{ $s->id }}">৳ {{ number_format($row['absent_deduction'], 2) }}</td>
                    <td style="text-align:right" id="gross-{{ $s->id }}">৳ {{ number_format($row['gross_salary'], 2) }}</td>
                    <td style="text-align:right">
                        <input type="number"
                            class="inline-input advance-input"
                            id="advance-{{ $s->id }}"
                            value="{{ $row['advance_cut'] }}"
                            step="0.01"
                            data-staff="{{ $s->id }}"
                            data-year="{{ $year }}"
                            data-month="{{ $month }}"
                            oninput="scheduleUpdate(this)"
                            placeholder="0.00">
                    </td>
                    <td style="text-align:right" class="net-payable" id="net-{{ $s->id }}">
                        ৳ {{ number_format($row['net_payable'], 2) }}
                    </td>
                    <td>
                        <input type="text"
                            class="inline-input remarks-input"
                            id="remarks-{{ $s->id }}"
                            value="{{ $row['remarks'] }}"
                            data-staff="{{ $s->id }}"
                            data-year="{{ $year }}"
                            data-month="{{ $month }}"
                            oninput="scheduleUpdate(document.getElementById('advance-{{ $s->id }}'))"
                            placeholder="Optional note">
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="text-align:center;padding:40px;color:var(--text-muted)">
                        No active staff found. <a href="{{ route('salary.staff.index') }}">Add staff members</a> first.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if(count($rows))
            <tfoot>
                <tr class="totals-row">
                    <td colspan="7" style="text-align:right;font-size:13px;letter-spacing:.05em">TOTALS</td>
                    <td style="text-align:right" id="total-gross">
                        ৳ {{ number_format(collect($rows)->sum('gross_salary'), 2) }}
                    </td>
                    <td style="text-align:right" id="total-advance">
                        ৳ {{ number_format(collect($rows)->sum('advance_cut'), 2) }}
                    </td>
                    <td style="text-align:right" id="total-net">
                        ৳ {{ number_format(collect($rows)->sum('net_payable'), 2) }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const CSRF = document.querySelector('meta[name=csrf-token]').content;
    const timers = {};

    function scheduleUpdate(input) {
        const staffId = input.dataset.staff;
        clearTimeout(timers[staffId]);
        // Show saving indicator
        const netEl = document.getElementById(`net-${staffId}`);
        netEl.classList.add('saving-dot');

        timers[staffId] = setTimeout(() => doUpdate(staffId, input.dataset.year, input.dataset.month), 600);
    }

    function doUpdate(staffId, year, month) {
        const advance = parseFloat(document.getElementById(`advance-${staffId}`).value) || 0;
        const remarks = document.getElementById(`remarks-${staffId}`).value;

        fetch('{{ route("salary.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    staff_id: staffId,
                    year,
                    month,
                    advance_cut: advance,
                    remarks
                })
            })
            .then(r => r.json())
            .then(data => {
                if (!data.ok) return;
                document.getElementById(`absent-days-${staffId}`).textContent = data.absent_days;
                document.getElementById(`absent-ded-${staffId}`).textContent = '৳ ' + data.absent_deduction;
                document.getElementById(`gross-${staffId}`).textContent = '৳ ' + data.gross_salary;
                document.getElementById(`net-${staffId}`).textContent = '৳ ' + data.net_payable;
                document.getElementById(`net-${staffId}`).classList.remove('saving-dot');
                recalcTotals();
            });
    }

    function recalcTotals() {
        let totalNet = 0,
            totalGross = 0,
            totalAdv = 0;
        document.querySelectorAll('[id^="net-"]').forEach(el => {
            totalNet += parseFloat(el.textContent.replace(/[^0-9.]/g, '')) || 0;
        });
        document.querySelectorAll('[id^="gross-"]').forEach(el => {
            totalGross += parseFloat(el.textContent.replace(/[^0-9.]/g, '')) || 0;
        });
        document.querySelectorAll('.advance-input').forEach(el => {
            totalAdv += parseFloat(el.value) || 0;
        });
        const fmt = n => '৳ ' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        const tg = document.getElementById('total-gross');
        const ta = document.getElementById('total-advance');
        const tn = document.getElementById('total-net');
        if (tg) tg.textContent = fmt(totalGross);
        if (ta) ta.textContent = fmt(totalAdv);
        if (tn) tn.textContent = fmt(totalNet);
    }
</script>

@push('styles')
<style>
    @media print {

        .sidebar,
        .topbar,
        .card-header a,
        .card-header button,
        form {
            display: none !important;
        }

        .main-wrapper {
            margin-left: 0 !important;
        }

        .card {
            box-shadow: none;
            border: none;
        }

        .inline-input {
            border: none;
            background: transparent;
            width: auto;
        }
    }
</style>
@endpush
@endpush