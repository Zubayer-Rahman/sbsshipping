@extends('layouts.app')

@section('title', 'Items')
@section('page-title', 'Items')
@section('breadcrumb', 'Items / List Items')

@section('content')

{{-- ── Page Header ── --}}
<div style="display:flex;align-items:baseline;gap:12px;margin-bottom:20px;flex-wrap:wrap">
    <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">Items</h2>
    <span style="font-size:13px;color:var(--text-muted)">Manage your items</span>
</div>

{{-- ── Filters Bar ── --}}
<div class="card" style="margin-bottom:0;border-bottom:none;border-radius:var(--radius) var(--radius) 0 0">
    <div style="padding:14px 20px;display:flex;align-items:center;gap:8px">
        <button type="button" class="btn btn-outline btn-sm"
            onclick="document.getElementById('filterPanel').classList.toggle('open')"
            style="display:flex;align-items:center;gap:6px;font-size:13px">
            <i class="bi bi-funnel-fill" style="color:var(--primary)"></i> Filters
        </button>
    </div>
    <div id="filterPanel" style="display:none;padding:0 20px 16px">
        <form method="GET" action="{{ route('items.list') }}"
            style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
            <div class="form-group" style="min-width:180px;flex:1;margin:0">
                <label class="form-label" style="font-size:12px">Search</label>
                <input type="text" name="search" class="form-control"
                    placeholder="Item name or code..." value="{{ request('search') }}">
            </div>
            <div class="form-group" style="min-width:140px;flex:1;margin:0">
                <label class="form-label" style="font-size:12px">Item Type</label>
                <select name="item_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="Single" {{ request('item_type')=='Single'  ?'selected':'' }}>Single</option>
                    <option value="Combo" {{ request('item_type')=='Combo'   ?'selected':'' }}>Combo</option>
                    <option value="Service" {{ request('item_type')=='Service' ?'selected':'' }}>Service</option>
                </select>
            </div>
            <div style="display:flex;gap:8px;align-items:flex-end">
                <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                @if(request()->hasAny(['search','item_type']))
                <a href="{{ route('items.list') }}" class="btn btn-outline btn-sm">Clear</a>
                @endif
            </div>
        </form>
    </div>
    <div style="border-bottom:2px solid var(--border);position:relative">
        <div style="position:absolute;bottom:-2px;left:20px;width:80px;height:2px;background:var(--primary)"></div>
    </div>
</div>

{{-- ── All Items Section ── --}}
<div class="card" style="border-radius:0 0 var(--radius) var(--radius);border-top:none">

    {{-- Top action bar --}}
    <div style="padding:16px 20px 12px;display:flex;align-items:center;
                justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div style="display:flex;align-items:center;gap:8px">
            <i class="bi bi-grid-3x3-gap-fill" style="color:var(--text-muted);font-size:16px"></i>
            <span style="font-size:14px;font-weight:700;color:var(--text-primary)">All Items</span>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <a href="{{ route('items.create') }}" class="pill-btn pill-blue">
                <i class="bi bi-plus-lg"></i> Add
            </a>
            <a href="{{ route('items.list') }}?export=excel" class="pill-btn pill-purple">
                <i class="bi bi-download"></i> <span class="hide-xs">Download </span>Excel
            </a>
        </div>
    </div>

    {{-- Toolbar --}}
    <div style="padding:8px 20px 12px;display:flex;align-items:center;
                justify-content:space-between;flex-wrap:wrap;gap:10px;
                border-bottom:1px solid var(--border)">

        {{-- Per-page --}}
        <div style="display:flex;align-items:center;gap:8px">
            <span style="font-size:13px;color:var(--text-muted)">Show</span>
            <form method="GET" action="{{ route('items.list') }}" id="perPageForm">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="item_type" value="{{ request('item_type') }}">
                <select name="per_page" class="form-select"
                    style="width:70px;padding:5px 8px;font-size:13px"
                    onchange="document.getElementById('perPageForm').submit()">
                    @foreach([10,25,50,100] as $pp)
                    <option value="{{ $pp }}" {{ request('per_page',50)==$pp?'selected':'' }}>{{ $pp }}</option>
                    @endforeach
                </select>
            </form>
            <span style="font-size:13px;color:var(--text-muted)">entries</span>
        </div>

        {{-- Export buttons + live search --}}
        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
            <div class="export-btns">
                @foreach(['Export CSV'=>'bi-filetype-csv','Export Excel'=>'bi-file-earmark-excel','Print'=>'bi-printer','Column visibility'=>'bi-layout-three-columns','Export PDF'=>'bi-filetype-pdf'] as $label=>$icon)
                <button type="button" class="export-btn">
                    <i class="bi {{ $icon }}"></i>
                    <span class="hide-sm">{{ $label }}</span>
                </button>
                @endforeach
            </div>
            <input type="text" id="liveSearch" placeholder="Search ..."
                class="form-control"
                style="width:160px;padding:6px 12px;font-size:13px;min-width:0"
                value="{{ request('search') }}">
        </div>
    </div>

    {{-- Scrollable table wrapper --}}
    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;width:100%">
        <table id="itemsTable" style="min-width:900px;width:100%">
            <thead>
                <tr>
                    <th style="width:36px">
                        <input type="checkbox" id="selectAll"
                            style="accent-color:var(--primary);width:15px;height:15px">
                    </th>
                    <th style="width:70px">Item image</th>
                    <th style="width:110px">Action</th>
                    <th>Item</th>
                    <th>Business Location
                        <span style="display:inline-flex;align-items:center;justify-content:center;
                                     width:14px;height:14px;background:var(--primary);color:#fff;
                                     border-radius:50%;font-size:9px;margin-left:3px;vertical-align:middle">i</span>
                    </th>
                    <th style="text-align:right">Unit Purchase Price</th>
                    <th style="text-align:right">Billing Amount</th>
                    <th style="text-align:right">Current stock</th>
                    <th>Item Type</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Tax</th>
                    <th>Item Code</th>
                </tr>
            </thead>
            <tbody id="itemsBody">
                @forelse($items as $item)
                <tr>
                    <td>
                        <input type="checkbox" class="row-check"
                            style="accent-color:var(--primary);width:15px;height:15px">
                    </td>
                    <td>
                        <div style="width:42px;height:42px;background:var(--body-bg);
                                    border:1px solid var(--border);border-radius:6px;
                                    display:flex;align-items:center;justify-content:center">
                            <i class="bi bi-image" style="font-size:17px;color:#cbd5e1"></i>
                        </div>
                    </td>
                    <td>
                        <div style="position:relative;display:inline-block">
                            <button type="button" class="actions-btn" onclick="toggleDropdown(this)"
                                style="display:inline-flex;align-items:center;gap:5px;
                                           padding:5px 12px;border-radius:20px;
                                           border:1.5px solid var(--primary);
                                           background:#fff;color:var(--primary);
                                           font-size:12px;font-weight:600;cursor:pointer;
                                           white-space:nowrap">
                                Actions <i class="bi bi-chevron-down" style="font-size:10px"></i>
                            </button>
                            <div class="actions-dropdown"
                                style="display:none;position:absolute;top:calc(100% + 4px);left:0;
                                        background:#fff;border:1px solid var(--border);
                                        border-radius:8px;box-shadow:var(--shadow-md);
                                        min-width:130px;z-index:200;overflow:hidden">
                                <a href="{{ route('items.edit', $item) }}"
                                    style="display:flex;align-items:center;gap:8px;padding:9px 14px;
                                          font-size:13px;color:var(--text-primary);text-decoration:none"
                                    onmouseover="this.style.background='var(--body-bg)'"
                                    onmouseout="this.style.background='transparent'">
                                    <i class="bi bi-pencil" style="color:var(--primary)"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('items.destroy', $item) }}"
                                    onsubmit="return confirm('Delete this item?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        style="width:100%;display:flex;align-items:center;gap:8px;
                                                   padding:9px 14px;font-size:13px;color:var(--danger);
                                                   background:transparent;border:none;cursor:pointer;text-align:left"
                                        onmouseover="this.style.background='#fee2e2'"
                                        onmouseout="this.style.background='transparent'">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                    <td style="font-weight:600;font-size:13px">{{ $item->item_name }}</td>
                    <td style="font-size:13px">SBS Shipping</td>
                    <td style="font-size:13px;text-align:right">TK. {{ number_format($item->exc_tax ?? 0, 2) }}</td>
                    <td style="font-size:13px;text-align:right">TK. {{ number_format($item->billing_exc_tax ?? 0, 2) }}</td>
                    <td style="font-size:13px;text-align:right;
                               color:{{ ($item->current_stock ?? 0) < 0 ? '#dc2626' : '#0f172a' }}">
                        {{ number_format($item->current_stock ?? 0, 2) }} Nos
                    </td>
                    <td style="font-size:13px">{{ $item->item_type ?? 'Single' }}</td>
                    <td style="font-size:13px;color:var(--text-muted)">{{ $item->category ?? '' }}</td>
                    <td style="font-size:13px;color:var(--text-muted)">{{ $item->brand ?? '' }}</td>
                    <td style="font-size:13px;color:var(--text-muted)">
                        {{ $item->applicable_tax == 'None' ? '' : $item->applicable_tax }}
                    </td>
                    <td style="font-size:13px;font-weight:600;color:var(--primary)">
                        {{ $item->item_code ?? '' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" style="text-align:center;padding:48px;color:var(--text-muted)">
                        <i class="bi bi-box-seam" style="font-size:40px;display:block;margin-bottom:10px;opacity:.3"></i>
                        No items found.
                        <a href="{{ route('items.create') }}" style="color:var(--primary)">Add your first item →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($items->hasPages())
    <div class="pagination-wrapper" style="display:flex;align-items:center;
                justify-content:space-between;flex-wrap:wrap;gap:8px">
        <div style="font-size:13px;color:var(--text-muted)">
            Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} entries
        </div>
        {{ $items->withQueryString()->links() }}
    </div>
    @else
    <div style="padding:12px 20px;font-size:13px;color:var(--text-muted)">
        Showing {{ $items->count() }} entries
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
    /* Pill buttons */
    .pill-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 20px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all .2s;
        white-space: nowrap;
    }

    .pill-blue {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 4px 14px rgba(26, 86, 219, .3);
    }

    .pill-blue:hover {
        background: var(--primary-dark);
    }

    .pill-purple {
        background: #7c3aed;
        color: #fff;
        box-shadow: 0 4px 14px rgba(124, 58, 237, .25);
    }

    .pill-purple:hover {
        background: #6d28d9;
    }

    /* Export buttons */
    .export-btns {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .export-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
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

    .export-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    /* Filter panel toggle */
    #filterPanel.open {
        display: block !important;
    }

    /* Pagination */
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

    /* Responsive hide helpers */
    @media (max-width: 640px) {
        .hide-xs {
            display: none;
        }

        .export-btns {
            display: none;
        }

        /* hide export buttons on mobile, saves space */
    }

    @media (max-width: 900px) {
        .hide-sm {
            display: none;
        }

        /* icon-only export buttons on tablet */
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    });

    function toggleDropdown(btn) {
        const dd = btn.nextElementSibling;
        document.querySelectorAll('.actions-dropdown').forEach(d => {
            if (d !== dd) d.style.display = 'none';
        });
        dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.actions-btn') && !e.target.closest('.actions-dropdown')) {
            document.querySelectorAll('.actions-dropdown').forEach(d => d.style.display = 'none');
        }
    });

    document.getElementById('liveSearch').addEventListener('input', function() {
        const val = this.value.toLowerCase();
        document.querySelectorAll('#itemsBody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
        });
    });
</script>
@endpush