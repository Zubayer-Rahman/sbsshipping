@extends('layouts.app')

@section('title', 'Attendance Sheet')
@section('page-title', 'Attendance Sheet')
@section('breadcrumb', 'Salary / Attendance')

@push('styles')
<style>
    .att-table th,
    .att-table td {
        padding: 8px 10px;
        font-size: 12.5px;
        white-space: nowrap;
    }

    .att-table th {
        background: var(--body-bg);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: var(--text-muted);
    }

    .att-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 28px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all .15s;
        user-select: none;
    }

    .att-badge:hover {
        transform: scale(1.15);
    }

    .att-P {
        background: #d1fae5;
        color: #065f46;
        border-color: #6ee7b7;
    }

    .att-A {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fca5a5;
    }

    .att-L {
        background: #fef3c7;
        color: #92400e;
        border-color: #fcd34d;
    }

    .att-H {
        background: #e0e7ff;
        color: #3730a3;
        border-color: #a5b4fc;
    }

    .att-LV {
        background: #f3e8ff;
        color: #6b21a8;
        border-color: #c4b5fd;
    }

    .att-default {
        background: var(--body-bg);
        color: var(--text-muted);
        border-color: var(--border);
    }

    .att-saving {
        opacity: .5;
        pointer-events: none;
    }

    .month-nav {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .legend-dot {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 600;
    }

    .summary-bar {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .sum-pill {
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .sticky-col {
        position: sticky;
        left: 0;
        background: var(--card-bg);
        z-index: 2;
        border-right: 1px solid var(--border);
    }

    .sticky-head {
        position: sticky;
        left: 0;
        background: var(--body-bg);
        z-index: 3;
    }
</style>
@endpush

@section('content')

{{-- Month selector --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
        <div class="month-nav">
            @php
            $prevMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
            $nextMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
            @endphp
            <a href="{{ route('salary.attendance', ['year'=>$prevMonth->year,'month'=>$prevMonth->month]) }}" class="btn btn-outline btn-sm">
                <i class="bi bi-chevron-left"></i>
            </a>
            <span style="font-size:16px;font-weight:700;min-width:140px;text-align:center">
                {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}
            </span>
            <a href="{{ route('salary.attendance', ['year'=>$nextMonth->year,'month'=>$nextMonth->month]) }}" class="btn btn-outline btn-sm">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <form method="GET" action="{{ route('salary.attendance') }}" style="display:flex;gap:8px;align-items:center">
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

        <div style="margin-left:auto;display:flex;gap:12px;flex-wrap:wrap">
            <span class="legend-dot"><span class="att-badge att-P">P</span> Present</span>
            <span class="legend-dot"><span class="att-badge att-A">A</span> Absent</span>
            <span class="legend-dot"><span class="att-badge att-L">L</span> Late</span>
            <span class="legend-dot"><span class="att-badge att-H">H</span> Half Day</span>
            <span class="legend-dot"><span class="att-badge att-LV">LV</span> Leave</span>
        </div>

        <div style="margin-left:auto">
            <a href="{{ route('salary.sheet', ['year'=>$year,'month'=>$month]) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-table"></i> View Salary Sheet
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Click a cell to cycle through statuses &nbsp;·&nbsp; Weekends (Fri/Sat) excluded</h2>
    </div>
    <div class="table-wrapper" style="overflow-x:auto">
        <table class="att-table" style="width:100%;border-collapse:collapse">
            <thead>
                <tr>
                    <th class="sticky-head" style="min-width:160px">#&nbsp; Name</th>
                    @foreach($weekdays as $day)
                    <th style="text-align:center;min-width:46px">
                        <div>{{ $day->format('d') }}</div>
                        <div style="font-size:10px;color:var(--text-muted)">{{ $day->format('D') }}</div>
                    </th>
                    @endforeach
                    <th style="text-align:center;min-width:80px">Summary</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffList as $si => $s)
                @php $staffAtt = $existing->get($s->id, collect([])); @endphp
                <tr>
                    <td class="sticky-col">
                        <div style="font-weight:600">{{ $si+1 }}. {{ $s->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted)">{{ $s->position }}</div>
                    </td>
                    @foreach($weekdays as $day)
                    @php
                    $dateStr = $day->format('Y-m-d');
                    $att = $staffAtt->get($dateStr);
                    $status = $att ? $att->status : null;
                    $badgeClass = match($status) {
                    'present' => 'att-P',
                    'absent' => 'att-A',
                    'late' => 'att-L',
                    'half_day' => 'att-H',
                    'leave' => 'att-LV',
                    default => 'att-default',
                    };
                    $label = match($status) {
                    'present' => 'P',
                    'absent' => 'A',
                    'late' => 'L',
                    'half_day' => 'H',
                    'leave' => 'LV',
                    default => '·',
                    };
                    @endphp
                    <td style="text-align:center;padding:4px">
                        <span class="att-badge {{ $badgeClass }}"
                            data-staff="{{ $s->id }}"
                            data-date="{{ $dateStr }}"
                            data-status="{{ $status ?? '' }}"
                            onclick="cycleStatus(this)">{{ $label }}</span>
                    </td>
                    @endforeach
                    {{-- Summary --}}
                    <td style="text-align:center" id="summary-{{ $s->id }}">
                        @php
                        $pCount = $staffAtt->where('status','present')->count();
                        $aCount = $staffAtt->where('status','absent')->count();
                        $lCount = $staffAtt->where('status','late')->count();
                        $hCount = $staffAtt->where('status','half_day')->count();
                        $lvCount = $staffAtt->where('status','leave')->count();
                        @endphp
                        <div class="summary-bar" style="justify-content:center">
                            <span class="sum-pill att-P">P:{{ $pCount }}</span>
                            <span class="sum-pill att-A">A:{{ $aCount }}</span>
                            <span class="sum-pill att-H">H:{{ $hCount }}</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const CYCLE = ['', 'present', 'absent', 'late', 'half_day', 'leave'];
    const LABEL = {
        '': '·',
        present: 'P',
        absent: 'A',
        late: 'L',
        half_day: 'H',
        leave: 'LV'
    };
    const BADGE = {
        '': 'att-default',
        present: 'att-P',
        absent: 'att-A',
        late: 'att-L',
        half_day: 'att-H',
        leave: 'att-LV'
    };
    const CSRF = document.querySelector('meta[name=csrf-token]').content;

    function cycleStatus(el) {
        const current = el.dataset.status;
        const idx = CYCLE.indexOf(current);
        const next = CYCLE[(idx + 1) % CYCLE.length];

        // Optimistic UI update
        el.classList.remove(...Object.values(BADGE));
        el.classList.add(BADGE[next]);
        el.textContent = LABEL[next];
        el.dataset.status = next;

        el.classList.add('att-saving');

        fetch('{{ route("salary.attendance.mark") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    staff_id: el.dataset.staff,
                    date: el.dataset.date,
                    status: next || 'present', // default to present if cycling back to empty
                })
            })
            .then(r => r.json())
            .then(() => {
                el.classList.remove('att-saving');
                updateSummary(el.dataset.staff);
            })
            .catch(() => {
                el.classList.remove('att-saving');
            });
    }

    function updateSummary(staffId) {
        const row = document.querySelectorAll(`[data-staff="${staffId}"]`);
        let p = 0,
            a = 0,
            h = 0;
        row.forEach(el => {
            if (el.dataset.status === 'present') p++;
            if (el.dataset.status === 'absent') a++;
            if (el.dataset.status === 'half_day') h++;
        });
        const box = document.getElementById(`summary-${staffId}`);
        if (box) box.innerHTML = `
        <div class="summary-bar" style="justify-content:center">
            <span class="sum-pill att-P">P:${p}</span>
            <span class="sum-pill att-A">A:${a}</span>
            <span class="sum-pill att-H">H:${h}</span>
        </div>`;
    }
</script>
@endpush