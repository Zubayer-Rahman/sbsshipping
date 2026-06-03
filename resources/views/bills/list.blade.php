@extends('layouts.app')
@section('title','Bills')
@section('page-title','Bills')
@section('breadcrumb','Bill / All Bills')

@section('content')

<div style="display:flex;align-items:baseline;gap:12px;margin-bottom:20px">
    <h2 style="font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">Bills</h2>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:0;border-bottom:none;border-radius:var(--radius) var(--radius) 0 0">
    <div style="padding:14px 20px;display:flex;align-items:center;gap:8px">
        <button type="button" onclick="document.getElementById('billFilterPanel').classList.toggle('open')"
            class="btn btn-outline btn-sm" style="display:flex;align-items:center;gap:6px;font-size:13px">
            <i class="bi bi-funnel-fill" style="color:var(--primary)"></i> Filters
        </button>
    </div>
    <div id="billFilterPanel" style="display:none;padding:0 20px 18px">
        <form method="GET" action="{{ route('bills.list') }}"
            style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px 20px;align-items:end">
            <div class="form-group" style="margin:0">
                <label class="form-label" style="font-size:12px">Business Location:</label>
                <select name="location" class="form-select">
                    <option value="">All</option>
                    <option value="SBS Shipping (BL0001)" {{ request('location')=='SBS Shipping (BL0001)'?'selected':'' }}>SBS Shipping (BL0001)</option>
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label" style="font-size:12px">Client:</label>
                <select name="client" class="form-select">
                    <option value="">All</option>
                    @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client')==$c->id?'selected':'' }}>{{ $c->business_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label" style="font-size:12px">Payment Status:</label>
                <select name="payment_status" class="form-select">
                    <option value="">All</option>
                    <option value="Paid" {{ request('payment_status')=='Paid'   ?'selected':'' }}>Paid</option>
                    <option value="Due" {{ request('payment_status')=='Due'    ?'selected':'' }}>Due</option>
                    <option value="Partial" {{ request('payment_status')=='Partial'?'selected':'' }}>Partial</option>
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label" style="font-size:12px">Payment Method:</label>
                <select name="payment_method" class="form-select">
                    <option value="">All</option>
                    @foreach(['Cash','Bank Transfer','Cheque','bKash','Nagad'] as $m)
                    <option value="{{ $m }}" {{ request('payment_method')==$m?'selected':'' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label" style="font-size:12px">Date From:</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="form-group" style="margin:0">
                <label class="form-label" style="font-size:12px">Date To:</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div style="display:flex;gap:8px;align-items:flex-end">
                <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                @if(request()->hasAny(['client','payment_status','payment_method','date_from','date_to']))
                <a href="{{ route('bills.list') }}" class="btn btn-outline btn-sm">Clear</a>
                @endif
            </div>
        </form>
    </div>
    <div style="border-bottom:2px solid var(--border);position:relative">
        <div style="position:absolute;bottom:-2px;left:20px;width:80px;height:2px;background:var(--primary)"></div>
    </div>
</div>

{{-- Table card --}}
<div class="card" style="border-radius:0 0 var(--radius) var(--radius);border-top:none">

    {{-- Toolbar --}}
    <div style="padding:14px 20px 12px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <span style="font-size:14px;font-weight:700;color:var(--text-primary)">All Bills</span>
        <a href="{{ route('bills.create') }}"
            style="display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:30px;
                  background:#7c3aed;color:#fff;font-size:13px;font-weight:700;text-decoration:none;
                  box-shadow:0 4px 14px rgba(124,58,237,.25)">
            <i class="bi bi-plus-lg"></i> Add
        </a>
    </div>

    <div style="padding:6px 20px 12px;display:flex;align-items:center;justify-content:space-between;
                flex-wrap:wrap;gap:8px;border-bottom:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:8px">
            <span style="font-size:13px;color:var(--text-muted)">Show</span>
            <form method="GET" action="{{ route('bills.list') }}" id="ppForm">
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
            <button type="button" class="bill-tb-btn"><i class="bi {{ $ic }}"></i> <span class="hide-sm">{{ $lbl }}</span></button>
            @endforeach
            <input type="text" id="liveSearch" placeholder="Search ..."
                class="form-control" style="width:160px;padding:6px 12px;font-size:13px">
        </div>
    </div>

    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch">
        <table style="width:100%;min-width:1300px;border-collapse:collapse;font-size:13px" id="billTable">
            <thead>
                <tr style="background:#f0f4ff">
                    <th class="bill-th">Action</th>
                    <th class="bill-th">Date <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th">Bill No. <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th">Client name <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th">Contact Number <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th">Location <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th">Payment Status <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th">Payment Method <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th" style="text-align:right">Total amount <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th" style="text-align:right">Total paid <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th" style="text-align:right">Total Due <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th">Sell Return Due <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th">Shipping Status <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th" style="text-align:right">Total Items <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th">Job Number <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th">Added By <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th">Billing note <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                    <th class="bill-th">Staff note <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                </tr>
            </thead>
            <tbody id="billBody">
                @forelse($bills as $bill)
                <tr style="border-bottom:1px solid var(--border);transition:background .12s"
                    onmouseover="this.style.background='#f8faff'" onmouseout="this.style.background=''">
                    <td class="bill-td">
                        <div style="position:relative;display:inline-block">
                            <button type="button" class="bill-act-btn" onclick="billToggle(this)"
                                style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;
                                           border-radius:20px;border:1.5px solid var(--primary);
                                           background:#fff;color:var(--primary);font-size:12px;font-weight:600;cursor:pointer">
                                Actions <i class="bi bi-chevron-down" style="font-size:10px"></i>
                            </button>
                            <div class="bill-dd" style="display:none">
                                <a href="{{ route('bills.show', $bill) }}" class="bill-dd-item"><i class="bi bi-eye" style="color:var(--primary)"></i> View</a>
                                    @csrf @method('DELETE')
                                    <button type="submit" class="bill-dd-item bill-dd-btn"><i class="bi bi-trash" style="color:var(--danger)"></i> Delete</button>
                                </form>
                            </div>
                        </div>
                    </td>
                    <td class="bill-td" style="white-space:nowrap;font-size:12px">{{ $bill->billing_date->format('d/m/Y h:i A') }}</td>
                    <td class="bill-td" style="font-weight:700;color:var(--primary)">{{ $bill->bill_no }}</td>
                    <td class="bill-td" style="font-weight:600">{{ $bill->client_name ?? '—' }}</td>
                    <td class="bill-td" style="font-size:12px">{{ $bill->client_contact ?? '—' }}</td>
                    <td class="bill-td">{{ $bill->business_location }}</td>
                    <td class="bill-td">
                        @php $pc=['Paid'=>'background:#d1fae5;color:#065f46','Due'=>'background:#fef3c7;color:#92400e','Partial'=>'background:#dbeafe;color:#1e40af']; @endphp
                        <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;{{ $pc[$bill->payment_status]??'' }}">
                            {{ $bill->payment_status }}
                        </span>
                    </td>
                    <td class="bill-td" style="font-size:12px">{{ $bill->payment_method ?? '—' }}</td>
                    <td class="bill-td" style="text-align:right;font-weight:600">TK. {{ number_format($bill->total_payable,2) }}</td>
                    <td class="bill-td" style="text-align:right">TK. {{ number_format($bill->total_paid,2) }}</td>
                    <td class="bill-td" style="text-align:right;color:{{ $bill->total_remaining>0?'var(--danger)':'var(--success)' }};font-weight:600">
                        TK. {{ number_format($bill->total_remaining,2) }}
                    </td>
                    <td class="bill-td" style="font-size:12px">—</td>
                    <td class="bill-td" style="font-size:12px">{{ $bill->shipping_status ?? '—' }}</td>
                    <td class="bill-td" style="text-align:right">{{ number_format($bill->total_items,2) }}</td>
                    <td class="bill-td" style="font-size:12px;color:var(--primary);font-weight:600">{{ $bill->job_number ?? '—' }}</td>
                    <td class="bill-td" style="font-size:12px">{{ $bill->added_by ?? '—' }}</td>
                    <td class="bill-td" style="font-size:12px;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $bill->billing_note ?? '—' }}</td>
                    <td class="bill-td" style="font-size:12px">{{ $bill->staff_note ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="18" style="text-align:center;padding:52px;color:var(--text-muted)">
                        <i class="bi bi-receipt" style="font-size:42px;display:block;margin-bottom:10px;opacity:.3"></i>
                        No bills found. <a href="{{ route('bills.create') }}" style="color:var(--primary)">Create your first bill →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Totals footer --}}
    @if($bills->count() > 0)
    @php
    $totalAmt = $bills->sum('total_payable');
    $totalPaid = $bills->sum('total_paid');
    $totalDue = $bills->sum('total_remaining');
    $statusSummary = $bills->groupBy('payment_status')->map->count();
    $methodSummary = $bills->groupBy('payment_method')->map->sum('total_paid');
    @endphp
    <div style="padding:12px 20px;background:#f8faff;border-top:2px solid var(--border);
                display:flex;align-items:center;justify-content:flex-end;gap:24px;flex-wrap:wrap;
                font-size:13px;font-weight:600">
        <span>
            @foreach($statusSummary as $status => $count)
            <span style="margin-right:8px">{{ $status }} - {{ $count }}</span>
            @endforeach
            @foreach($methodSummary as $method => $amt)
            | {{ $method }} - {{ $amt }}
            @endforeach
        </span>
        <span>TK. {{ number_format($totalAmt,2) }}</span>
        <span>TK. {{ number_format($totalPaid,2) }}</span>
        <span style="color:var(--danger)">TK. {{ number_format($totalDue,2) }}</span>
        <span>TK. 0.00</span>
    </div>
    @endif

    @if($bills->hasPages())
    <div style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;border-top:1px solid var(--border)">
        <span style="font-size:13px;color:var(--text-muted)">Showing {{ $bills->firstItem() }}–{{ $bills->lastItem() }} of {{ $bills->total() }} entries</span>
        {{ $bills->withQueryString()->links() }}
    </div>
    @else
    <div style="padding:12px 20px;font-size:13px;color:var(--text-muted)">Showing {{ $bills->count() }} entries</div>
    @endif
</div>

{{-- Bill detail modal --}}
<div id="billModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;overflow-y:auto;padding:20px">
    <div style="background:#fff;max-width:960px;margin:0 auto;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative">
        <div id="billModalContent" style="padding:28px"></div>
        <button onclick="document.getElementById('billModal').style.display='none'"
            style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:22px;cursor:pointer;color:var(--text-muted)">×</button>
    </div>
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
        font-family: 'Inter', sans-serif !important
    }

    .bill-th {
        padding: 11px 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        border-bottom: 2px solid var(--border);
        text-align: left;
        white-space: nowrap
    }

    .bill-td {
        padding: 10px 12px;
        vertical-align: middle
    }

    .bill-tb-btn {
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
        transition: all .15s
    }

    .bill-tb-btn:hover {
        border-color: var(--primary);
        color: var(--primary)
    }

    #billFilterPanel.open {
        display: block !important
    }

    nav[role="navigation"] {
        display: flex;
        align-items: center;
        justify-content: flex-end
    }

    .pagination {
        display: flex;
        gap: 4px;
        list-style: none;
        margin: 0
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
        transition: all .15s
    }

    .pagination li a:hover {
        border-color: var(--primary);
        color: var(--primary)
    }

    .pagination li.active span {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary)
    }

    .bill-dd {
        display: none;
        position: fixed;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(15, 31, 75, .14);
        min-width: 180px;
        z-index: 99999;
        padding: 6px 0
    }

    .bill-dd-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        font-size: 13px;
        color: var(--text-primary);
        text-decoration: none;
        transition: background .15s;
        white-space: nowrap
    }

    .bill-dd-item:hover {
        background: #f0f4ff
    }

    .bill-dd-btn {
        background: none;
        border: none;
        width: 100%;
        cursor: pointer;
        text-align: left;
        font-family: inherit
    }

    @media(max-width:640px) {
        .hide-sm {
            display: none
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let _billBtn = null;
    const billFloat = document.createElement('div');
    billFloat.style.cssText = 'display:none;position:fixed;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(15,31,75,.16);min-width:180px;z-index:99999;padding:6px 0;';
    document.body.appendChild(billFloat);

    function billToggle(btn) {
        const src = btn.nextElementSibling;
        if (_billBtn === btn) {
            billClose();
            return;
        }
        billClose();
        billFloat.innerHTML = src.innerHTML;
        billFloat.style.display = 'block';
        const r = btn.getBoundingClientRect();
        billFloat.style.top = (r.bottom + 4) + 'px';
        billFloat.style.left = (r.left + billFloat.offsetWidth > window.innerWidth - 8 ? r.right - billFloat.offsetWidth : r.left) + 'px';
        _billBtn = btn;
    }

    function billClose() {
        billFloat.style.display = 'none';
        billFloat.innerHTML = '';
        _billBtn = null;
    }
    document.addEventListener('click', e => {
        if (!e.target.closest('.bill-act-btn') && !billFloat.contains(e.target)) billClose();
    });
    window.addEventListener('scroll', () => {
        if (_billBtn) {
            const r = _billBtn.getBoundingClientRect();
            billFloat.style.top = (r.bottom + 4) + 'px';
        }
    }, true);

    document.getElementById('liveSearch').addEventListener('input', function() {
        const v = this.value.toLowerCase();
        document.querySelectorAll('#billBody tr').forEach(r => r.style.display = r.textContent.toLowerCase().includes(v) ? '' : 'none');
    });
</script>
@endpush