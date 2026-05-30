@extends('layouts.app')
@section('title','Purchases')
@section('page-title','Purchases')
@section('breadcrumb','Expenses / Purchases')

@section('content')

<div style="display:flex;align-items:baseline;gap:12px;margin-bottom:20px;flex-wrap:wrap">
    <h2 style="font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">Purchases</h2>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:0;border-bottom:none;border-radius:var(--radius) var(--radius) 0 0">
    <div style="padding:14px 20px;display:flex;align-items:center;gap:8px">
        <button type="button" onclick="document.getElementById('purFilterPanel').classList.toggle('open')"
            class="btn btn-outline btn-sm" style="display:flex;align-items:center;gap:6px;font-size:13px">
            <i class="bi bi-funnel-fill" style="color:var(--primary)"></i> Filters
        </button>
    </div>
    <div id="purFilterPanel" style="display:none;padding:0 20px 16px">
        <form method="GET" action="{{ route('purchases.list') }}"
            style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
            <div class="form-group" style="min-width:180px;flex:1;margin:0">
                <label class="form-label" style="font-size:12px">Search</label>
                <input type="text" name="search" class="form-control"
                    placeholder="Ref no, supplier..." value="{{ request('search') }}">
            </div>
            <div class="form-group" style="min-width:140px;margin:0">
                <label class="form-label" style="font-size:12px">Payment Status</label>
                <select name="payment_status" class="form-select">
                    <option value="">All</option>
                    <option value="Paid" {{ request('payment_status')=='Paid'    ?'selected':'' }}>Paid</option>
                    <option value="Due" {{ request('payment_status')=='Due'     ?'selected':'' }}>Due</option>
                    <option value="Partial" {{ request('payment_status')=='Partial' ?'selected':'' }}>Partial</option>
                </select>
            </div>
            <div class="form-group" style="min-width:140px;margin:0">
                <label class="form-label" style="font-size:12px">From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="form-group" style="min-width:140px;margin:0">
                <label class="form-label" style="font-size:12px">To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Apply</button>
            @if(request()->hasAny(['search','payment_status','date_from','date_to']))
            <a href="{{ route('purchases.list') }}" class="btn btn-outline btn-sm">Clear</a>
            @endif
        </form>
    </div>
    <div style="border-bottom:2px solid var(--border);position:relative">
        <div style="position:absolute;bottom:-2px;left:20px;width:80px;height:2px;background:var(--primary)"></div>
    </div>
</div>

<div class="card" style="border-radius:0 0 var(--radius) var(--radius);border-top:none">

    {{-- Toolbar --}}
    <div style="padding:14px 20px 12px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <span style="font-size:14px;font-weight:700;color:var(--text-primary)">All Purchases</span>
        <a href="{{ route('purchases.create') }}"
            style="display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:30px;
                  background:#7c3aed;color:#fff;font-size:13px;font-weight:700;text-decoration:none;
                  box-shadow:0 4px 14px rgba(124,58,237,.25)">
            <i class="bi bi-plus-lg"></i> Add
        </a>
    </div>

    {{-- Show + Export + Search --}}
    <div style="padding:6px 20px 12px;display:flex;align-items:center;justify-content:space-between;
                flex-wrap:wrap;gap:8px;border-bottom:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:8px">
            <span style="font-size:13px;color:var(--text-muted)">Show</span>
            <form method="GET" action="{{ route('purchases.list') }}" id="ppForm">
                <select name="per_page" class="form-select" style="width:70px;padding:5px 8px;font-size:13px"
                    onchange="document.getElementById('ppForm').submit()">
                    @foreach([10,25,50,100] as $pp)
                    <option value="{{ $pp }}" {{ request('per_page',50)==$pp?'selected':'' }}>{{ $pp }}</option>
                    @endforeach
                </select>
            </form>
            <span style="font-size:13px;color:var(--text-muted)">entries</span>
        </div>
        <div style="display:flex;gap:5px;flex-wrap:wrap;align-items:center">
            @foreach(['Export CSV'=>'bi-filetype-csv','Export Excel'=>'bi-file-earmark-excel','Print'=>'bi-printer','Column visibility'=>'bi-layout-three-columns','Export PDF'=>'bi-filetype-pdf'] as $lbl=>$ic)
            <button type="button" class="pur-tb-btn"><i class="bi {{ $ic }}"></i> <span class="hide-sm">{{ $lbl }}</span></button>
            @endforeach
            <input type="text" id="liveSearch" placeholder="Search ..."
                class="form-control" style="width:160px;padding:6px 12px;font-size:13px"
                value="{{ request('search') }}">
        </div>
    </div>

    {{-- Table --}}
    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch">
        <table style="width:100%;min-width:1000px;border-collapse:collapse;font-size:13px" id="purTable">
            <thead>
                <tr style="background:#f0f4ff">
                    <th class="pur-th">Action</th>
                    <th class="pur-th">Date <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="pur-th">Reference No <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="pur-th">Location</th>
                    <th class="pur-th">Supplier <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="pur-th">Purchase Status <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="pur-th">Payment Status <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="pur-th" style="text-align:right">Grand Total <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="pur-th">Payment due
                        <span style="display:inline-flex;align-items:center;justify-content:center;
                                     width:14px;height:14px;background:var(--primary);color:#fff;
                                     border-radius:50%;font-size:9px;margin-left:2px">i</span>
                    </th>
                    <th class="pur-th">Added By <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                </tr>
            </thead>
            <tbody id="purBody">
                @forelse($purchases as $purchase)
                <tr style="border-bottom:1px solid var(--border);transition:background .12s"
                    onmouseover="this.style.background='#f8faff'" onmouseout="this.style.background=''">
                    <td class="pur-td">
                        <div style="position:relative;display:inline-block">
                            <button type="button" class="pur-act-btn" onclick="purToggle(this)"
                                style="display:inline-flex;align-items:center;gap:5px;
                                           padding:4px 12px;border-radius:20px;
                                           border:1.5px solid var(--primary);background:#fff;
                                           color:var(--primary);font-size:12px;font-weight:600;cursor:pointer">
                                Actions <i class="bi bi-chevron-down" style="font-size:10px"></i>
                            </button>
                            <div class="pur-dd" style="display:none">
                                <a href="{{ route('purchases.show', $purchase) }}"
                                    class="pur-dd-item" target="_blank">
                                    <i class="bi bi-eye" style="color:var(--primary)"></i> View
                                </a>
                                <form method="POST" action="{{ route('purchases.destroy', $purchase) }}"
                                    onsubmit="return confirm('Delete this purchase?')" style="margin:0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="pur-dd-item pur-dd-btn">
                                        <i class="bi bi-trash" style="color:var(--danger)"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                    <td class="pur-td" style="white-space:nowrap;font-size:12px">
                        {{ $purchase->purchase_date->format('d/m/Y h:i A') }}
                    </td>
                    <td class="pur-td" style="font-weight:700;color:var(--primary)">
                        {{ $purchase->reference_no }}
                    </td>
                    <td class="pur-td">{{ $purchase->business_location }}</td>
                    <td class="pur-td" style="font-weight:600">{{ $purchase->supplier_name ?? '—' }}</td>
                    <td class="pur-td">
                        <span style="display:inline-block;padding:3px 10px;border-radius:20px;
                                     font-size:11px;font-weight:700;
                                     background:#d1fae5;color:#065f46">
                            {{ $purchase->purchase_status }}
                        </span>
                    </td>
                    <td class="pur-td">
                        @php
                        $pc = ['Paid'=>'background:#d1fae5;color:#065f46','Due'=>'background:#fee2e2;color:#991b1b','Partial'=>'background:#fef3c7;color:#92400e'];
                        $ps = $purchase->payment_status;
                        @endphp
                        <span style="display:inline-block;padding:3px 10px;border-radius:20px;
                                     font-size:11px;font-weight:700;{{ $pc[$ps] ?? '' }}">
                            {{ $ps }}
                        </span>
                    </td>
                    <td class="pur-td" style="text-align:right;font-weight:700">
                        ৳ {{ number_format($purchase->payment_amount, 2) }}
                    </td>
                    <td class="pur-td" style="font-size:12px">
                        @php $due = $purchase->grand_total - $purchase->payment_amount; @endphp
                        <span style="color: {{ $due > 0 ? 'var(--danger)' : 'var(--success)' }};font-weight:600">
                            ৳ {{ number_format(max(0,$due), 2) }}
                        </span>
                    </td>
                    <td class="pur-td" style="font-size:12px">{{ $purchase->added_by ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:48px;color:var(--text-muted)">
                        <i class="bi bi-bag" style="font-size:40px;display:block;margin-bottom:10px;opacity:.3"></i>
                        No purchases found.
                        <a href="{{ route('purchases.create') }}" style="color:var(--primary)">Add your first purchase →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($purchases->hasPages())
    <div style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;border-top:1px solid var(--border)">
        <span style="font-size:13px;color:var(--text-muted)">Showing {{ $purchases->firstItem() }}–{{ $purchases->lastItem() }} of {{ $purchases->total() }}</span>
        {{ $purchases->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    body,
    input,
    select,
    textarea,
    button {
        font-family: 'Inter', sans-serif !important;
    }

    .pur-th {
        padding: 11px 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        border-bottom: 2px solid var(--border);
        text-align: left;
        white-space: nowrap;
    }

    .pur-td {
        padding: 10px 12px;
        vertical-align: middle;
    }

    .pur-tb-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 5px;
        background: #f8fafc;
        border: 1px solid var(--border);
        font-size: 12px;
        font-weight: 500;
        color: var(--text-muted);
        cursor: pointer;
        transition: all .15s;
    }

    .pur-tb-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    #purFilterPanel.open {
        display: block !important;
    }

    nav[role="navigation"] {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .pagination {
        display: flex;
        gap: 4px;
        list-style: none;
        margin: 0;
    }

    .pagination li a,
    .pagination li span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid var(--border);
        color: var(--text-muted);
        text-decoration: none;
        transition: all .15s;
    }

    .pagination li a:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .pagination li.active span {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    .pur-dd {
        display: none;
        position: fixed;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(15, 31, 75, .14);
        min-width: 130px;
        z-index: 99999;
        padding: 6px 0;
    }

    .pur-dd-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        font-size: 13px;
        color: var(--text-primary);
        text-decoration: none;
        transition: background .15s;
        white-space: nowrap;
    }

    .pur-dd-item:hover {
        background: #f0f4ff;
    }

    .pur-dd-btn {
        background: none;
        border: none;
        width: 100%;
        cursor: pointer;
        text-align: left;
        font-family: inherit;
    }

    @media(max-width:640px) {
        .hide-sm {
            display: none;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let _purBtn = null;
    const purFloat = document.createElement('div');
    purFloat.style.cssText = 'display:none;position:fixed;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(15,31,75,.16);min-width:140px;z-index:99999;padding:6px 0;';
    document.body.appendChild(purFloat);

    function purToggle(btn) {
        const src = btn.nextElementSibling;
        if (_purBtn === btn) {
            purClose();
            return;
        }
        purClose();
        purFloat.innerHTML = src.innerHTML;
        purFloat.style.display = 'block';
        const r = btn.getBoundingClientRect();
        purFloat.style.top = (r.bottom + 4) + 'px';
        purFloat.style.left = (r.left + purFloat.offsetWidth > window.innerWidth - 8 ? r.right - purFloat.offsetWidth : r.left) + 'px';
        _purBtn = btn;
    }

    function purClose() {
        purFloat.style.display = 'none';
        purFloat.innerHTML = '';
        _purBtn = null;
    }
    document.addEventListener('click', e => {
        if (!e.target.closest('.pur-act-btn') && !purFloat.contains(e.target)) purClose();
    });
    window.addEventListener('scroll', () => {
        if (_purBtn) {
            const r = _purBtn.getBoundingClientRect();
            purFloat.style.top = (r.bottom + 4) + 'px';
        }
    }, true);

    document.getElementById('liveSearch').addEventListener('input', function() {
        const v = this.value.toLowerCase();
        document.querySelectorAll('#purBody tr').forEach(r => r.style.display = r.textContent.toLowerCase().includes(v) ? '' : 'none');
    });
</script>
@endpush