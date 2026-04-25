@extends('layouts.app')
@section('title','Expenses')
@section('page-title','Expenses')
@section('breadcrumb','Expenses / List Expenses')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:baseline;gap:12px;margin-bottom:20px;flex-wrap:wrap">
    <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">Expenses</h2>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:0;border-bottom:none;border-radius:var(--radius) var(--radius) 0 0">
    <div style="padding:14px 20px;display:flex;align-items:center;gap:8px">
        <button type="button" onclick="document.getElementById('filterPanel').classList.toggle('open')"
            class="btn btn-outline btn-sm" style="display:flex;align-items:center;gap:6px;font-size:13px">
            <i class="bi bi-funnel-fill" style="color:var(--primary)"></i> Filters
        </button>
    </div>
    <div id="filterPanel" style="display:none;padding:0 20px 16px">
        <form method="GET" action="{{ route('expenses.list') }}"
            style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
            <div class="form-group" style="min-width:180px;flex:1;margin:0">
                <label class="form-label" style="font-size:12px">Search</label>
                <input type="text" name="search" class="form-control"
                    placeholder="Ref no, job, category..." value="{{ request('search') }}">
            </div>
            <div class="form-group" style="min-width:150px;margin:0">
                <label class="form-label" style="font-size:12px">Category</label>
                <select name="category" class="form-select">
                    <option value="">All</option>
                    <option value="Job Expense" {{ request('category')=='Job Expense'    ?'selected':'' }}>Job Expense</option>
                    <option value="Office Expense" {{ request('category')=='Office Expense' ?'selected':'' }}>Office Expense</option>
                </select>
            </div>
            <div class="form-group" style="min-width:140px;margin:0">
                <label class="form-label" style="font-size:12px">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="Paid" {{ request('status')=='Paid'    ?'selected':'' }}>Paid</option>
                    <option value="Due" {{ request('status')=='Due'     ?'selected':'' }}>Due</option>
                    <option value="Partial" {{ request('status')=='Partial' ?'selected':'' }}>Partial</option>
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
            @if(request()->hasAny(['search','category','status','date_from','date_to']))
            <a href="{{ route('expenses.list') }}" class="btn btn-outline btn-sm">Clear</a>
            @endif
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
        <div style="display:flex;align-items:center;gap:6px">
            <span style="font-size:14px;font-weight:700;color:var(--text-primary)">All expenses</span>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a href="#" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:30px;background:#10b981;color:#fff;font-size:13px;font-weight:700;text-decoration:none">
                <i class="bi bi-printer"></i> Print PDF
            </a>
            <a href="#" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:30px;background:var(--primary);color:#fff;font-size:13px;font-weight:700;text-decoration:none">
                <i class="bi bi-upload"></i> Import expense
            </a>
            <a href="{{ route('expenses.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:30px;background:#7c3aed;color:#fff;font-size:13px;font-weight:700;text-decoration:none">
                <i class="bi bi-plus-lg"></i> Add
            </a>
        </div>
    </div>

    {{-- Show + Export + Search --}}
    <div style="padding:6px 20px 12px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;border-bottom:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:8px">
            <span style="font-size:13px;color:var(--text-muted)">Show</span>
            <form method="GET" action="{{ route('expenses.list') }}" id="ppForm">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
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
            <button type="button" class="exp-toolbar-btn"><i class="bi {{ $ic }}"></i> <span class="hide-sm">{{ $lbl }}</span></button>
            @endforeach
            <input type="text" id="liveSearch" placeholder="Search ..."
                class="form-control" style="width:160px;padding:6px 12px;font-size:13px"
                value="{{ request('search') }}">
        </div>
    </div>

    {{-- Table --}}
    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch">
        <table style="width:100%;min-width:1100px;border-collapse:collapse;font-size:13px" id="expTable">
            <thead>
                <tr style="background:#f0f4ff">
                    <th class="exp-th">Action</th>
                    <th class="exp-th">Date</th>
                    <th class="exp-th">Job/Ref. No</th>
                    <th class="exp-th">Recurring details</th>
                    <th class="exp-th">Expense Category</th>
                    <th class="exp-th">Sub category</th>
                    <th class="exp-th" style="text-align:right">Total amount</th>
                    <th class="exp-th">Expense for</th>
                    <th class="exp-th">Contact</th>
                    <th class="exp-th">Expense note</th>
                    <th class="exp-th">Added By</th>
                </tr>
            </thead>
            <tbody id="expBody">
                @forelse($expenses as $exp)
                <tr style="border-bottom:1px solid var(--border);transition:background .12s"
                    onmouseover="this.style.background='#f8faff'" onmouseout="this.style.background=''">
                    <td class="exp-td">
                        <div style="position:relative;display:inline-block">
                            <button type="button" class="exp-actions-btn" onclick="expToggle(this)"
                                style="display:inline-flex;align-items:center;gap:5px;
                                           padding:4px 12px;border-radius:20px;
                                           border:1.5px solid var(--primary);background:#fff;
                                           color:var(--primary);font-size:12px;font-weight:600;cursor:pointer">
                                Actions <i class="bi bi-chevron-down" style="font-size:10px"></i>
                            </button>
                            <div class="exp-dd" style="display:none">
                                <a href="{{ route('expenses.edit', $exp) }}" class="exp-dd-item">
                                    <i class="bi bi-pencil" style="color:var(--primary)"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('expenses.destroy', $exp) }}"
                                    onsubmit="return confirm('Delete this expense?')" style="margin:0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="exp-dd-item exp-dd-btn">
                                        <i class="bi bi-trash" style="color:var(--danger)"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                    <td class="exp-td" style="white-space:nowrap">
                        {{ $exp->expense_date ? $exp->expense_date->format('d/m/Y h:i A') : '—' }}<br>
                        <span style="font-size:11px;color:var(--primary);font-weight:600">{{ $exp->expense_ref }}</span>
                    </td>
                    <td class="exp-td" style="font-weight:600;color:var(--primary)">{{ $exp->job_ref_no ?? '—' }}</td>
                    <td class="exp-td" style="font-size:12px">
                        {{ $exp->is_recurring ? 'Recurring' : '—' }}
                    </td>
                    <td class="exp-td">{{ $exp->expense_category ?? '—' }}</td>
                    <td class="exp-td" style="color:var(--text-muted)">{{ $exp->sub_category ?? '—' }}</td>
                    <td class="exp-td" style="text-align:right;font-weight:600">TK. {{ number_format($exp->total_amount,2) }}</td>
                    <td class="exp-td">{{ $exp->expense_for ?? '—' }}</td>
                    <td class="exp-td" style="font-size:12px">{{ $exp->expense_for_contact ?? '—' }}</td>
                    <td class="exp-td" style="max-width:180px;font-size:12px;color:var(--text-muted)">
                        {{ Str::limit($exp->expense_note, 60) }}
                    </td>
                    <td class="exp-td" style="font-size:12px">{{ $exp->added_by ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="text-align:center;padding:48px;color:var(--text-muted)">
                        <i class="bi bi-receipt" style="font-size:40px;display:block;margin-bottom:10px;opacity:.3"></i>
                        No expenses found.
                        <a href="{{ route('expenses.create') }}" style="color:var(--primary)">Add your first expense →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($expenses->hasPages())
    <div style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;border-top:1px solid var(--border)">
        <div style="font-size:13px;color:var(--text-muted)">
            Showing {{ $expenses->firstItem() }}–{{ $expenses->lastItem() }} of {{ $expenses->total() }} entries
        </div>
        {{ $expenses->withQueryString()->links() }}
    </div>
    @else
    <div style="padding:12px 20px;font-size:13px;color:var(--text-muted)">
        Showing {{ $expenses->count() }} entries
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
    .exp-th {
        padding: 11px 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        border-bottom: 2px solid var(--border);
        text-align: left;
        white-space: nowrap;
    }

    .exp-td {
        padding: 10px 12px;
        vertical-align: middle;
    }

    .exp-toolbar-btn {
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
        white-space: nowrap;
    }

    .exp-toolbar-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    #filterPanel.open {
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

    /* Floating dropdown (same body-level trick) */
    .exp-dd {
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

    .exp-dd-item {
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

    .exp-dd-item:hover {
        background: #f0f4ff;
    }

    .exp-dd-btn {
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
    let _expBtn = null,
        _expMenu = null;
    const expFloating = document.createElement('div');
    expFloating.style.cssText = 'display:none;position:fixed;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(15,31,75,.16);min-width:140px;z-index:99999;padding:6px 0;';
    document.body.appendChild(expFloating);

    function expToggle(btn) {
        const src = btn.nextElementSibling;
        if (_expBtn === btn) {
            expClose();
            return;
        }
        expClose();
        expFloating.innerHTML = src.innerHTML;
        expFloating.style.display = 'block';
        expPosition(btn);
        _expBtn = btn;
        _expMenu = expFloating;
    }

    function expPosition(btn) {
        const r = btn.getBoundingClientRect();
        expFloating.style.top = (r.bottom + 4) + 'px';
        expFloating.style.left = (r.left + expFloating.offsetWidth > window.innerWidth - 8 ? r.right - expFloating.offsetWidth : r.left) + 'px';
    }

    function expClose() {
        expFloating.style.display = 'none';
        expFloating.innerHTML = '';
        _expBtn = null;
        _expMenu = null;
    }
    document.addEventListener('click', e => {
        if (!e.target.closest('.exp-actions-btn') && !expFloating.contains(e.target)) expClose();
    });
    window.addEventListener('scroll', () => {
        if (_expBtn) expPosition(_expBtn);
    }, true);

    // Live search
    document.getElementById('liveSearch').addEventListener('input', function() {
        const v = this.value.toLowerCase();
        document.querySelectorAll('#expBody tr').forEach(r => r.style.display = r.textContent.toLowerCase().includes(v) ? '' : 'none');
    });
</script>
@endpush