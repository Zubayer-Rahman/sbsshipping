@extends('layouts.app')

@section('title', 'Items')
@section('page-title', 'Items')
@section('breadcrumb', 'Items / List Items')

@section('content')

<div style="max-width: 100%;">
    {{-- ── Page Header ── --}}
    <div style="display:flex;align-items:baseline;gap:12px;margin-bottom:20px">
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
        {{-- Filter panel (hidden by default) --}}
        <div id="filterPanel" style="display:none;padding:0 20px 16px">
            <form method="GET" action="{{ route('items.list') }}"
                style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
                <div class="form-group" style="min-width:200px;margin:0">
                    <label class="form-label" style="font-size:12px">Search</label>
                    <input type="text" name="search" class="form-control"
                        placeholder="Item name or code..." value="{{ request('search') }}">
                </div>
                <div class="form-group" style="min-width:160px;margin:0">
                    <label class="form-label" style="font-size:12px">Item Type</label>
                    <select name="item_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="Single" {{ request('item_type')=='Single'  ?'selected':'' }}>Single</option>
                        <option value="Combo" {{ request('item_type')=='Combo'   ?'selected':'' }}>Combo</option>
                        <option value="Service" {{ request('item_type')=='Service' ?'selected':'' }}>Service</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                @if(request()->hasAny(['search','item_type']))
                <a href="{{ route('items.list') }}" class="btn btn-outline btn-sm">Clear</a>
                @endif
            </form>
        </div>
        {{-- Blue underline tab --}}
        <div style="border-bottom:2px solid var(--border);position:relative">
            <div style="position:absolute;bottom:-2px;left:20px;
                    width:80px;height:2px;background:var(--primary)"></div>
        </div>
    </div>

    {{-- ── All Items Section ── --}}
    <div class="card" style="border-radius:0 0 var(--radius) var(--radius);border-top:none">
        <div style="padding:16px 20px 12px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div style="display:flex;align-items:center;gap:8px">
                <i class="bi bi-grid-3x3-gap-fill" style="color:var(--text-muted);font-size:16px"></i>
                <span style="font-size:14px;font-weight:700;color:var(--text-primary)">All Items</span>
            </div>
            <div style="display:flex;gap:10px">
                <a href="{{ route('items.create') }}"
                    style="display:inline-flex;align-items:center;gap:7px;
                      padding:9px 20px;border-radius:30px;
                      background:var(--primary);color:#fff;
                      font-size:13px;font-weight:700;text-decoration:none;
                      box-shadow:0 4px 14px rgba(26,86,219,.3);transition:all .2s"
                    onmouseover="this.style.background='var(--primary-dark)'"
                    onmouseout="this.style.background='var(--primary)'">
                    <i class="bi bi-plus-lg"></i> Add
                </a>
                <a href="{{ route('items.list') }}?export=excel"
                    style="display:inline-flex;align-items:center;gap:7px;
                      padding:9px 20px;border-radius:30px;
                      background:#7c3aed;color:#fff;
                      font-size:13px;font-weight:700;text-decoration:none;
                      box-shadow:0 4px 14px rgba(124,58,237,.25);transition:all .2s"
                    onmouseover="this.style.background='#6d28d9'"
                    onmouseout="this.style.background='#7c3aed'">
                    <i class="bi bi-download"></i> Download Excel
                </a>
            </div>
        </div>

        {{-- Toolbar: Show entries + Export buttons + Search --}}
        <div style="padding:8px 20px 12px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;border-bottom:1px solid var(--border)">
            <div style="display:flex;align-items:center;gap:8px">
                <span style="font-size:13px;color:var(--text-muted)">Show</span>
                <form method="GET" action="{{ route('items.list') }}" id="perPageForm">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="item_type" value="{{ request('item_type') }}">
                    <select name="per_page" class="form-select" style="width:70px;padding:5px 8px;font-size:13px"
                        onchange="document.getElementById('perPageForm').submit()">
                        @foreach([10,25,50,100] as $pp)
                        <option value="{{ $pp }}" {{ request('per_page', 50) == $pp ? 'selected':'' }}>{{ $pp }}</option>
                        @endforeach
                    </select>
                </form>
                <span style="font-size:13px;color:var(--text-muted)">entries</span>
            </div>

            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                {{-- Export buttons --}}
                @foreach(['Export CSV'=>'bi-filetype-csv','Export Excel'=>'bi-file-earmark-excel','Print'=>'bi-printer','Column visibility'=>'bi-layout-three-columns','Export PDF'=>'bi-filetype-pdf'] as $label => $icon)
                <button type="button"
                    style="display:inline-flex;align-items:center;gap:5px;
                           padding:5px 11px;border-radius:5px;
                           background:#f8fafc;border:1px solid var(--border);
                           font-size:12px;font-weight:500;color:var(--text-muted);
                           cursor:pointer;transition:all .15s"
                    onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                    onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-muted)'">
                    <i class="bi {{ $icon }}" style="font-size:12px"></i> {{ $label }}
                </button>
                @endforeach

                {{-- Search --}}
                <div style="position:relative">
                    <input type="text" id="liveSearch" placeholder="Search ..."
                        class="form-control" style="width:180px;padding:6px 12px;font-size:13px"
                        value="{{ request('search') }}">
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-wrapper">
            <table id="itemsTable">
                <thead>
                    <tr>
                        <th style="width:36px">
                            <input type="checkbox" id="selectAll" style="accent-color:var(--primary);width:15px;height:15px">
                        </th>
                        <th style="width:80px">Item image</th>
                        <th>Action <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                        <th>Item <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                        <th>Business Location
                            <span style="display:inline-flex;align-items:center;justify-content:center;
                                     width:14px;height:14px;background:var(--primary);color:#fff;
                                     border-radius:50%;font-size:9px;margin-left:3px;vertical-align:middle">i</span>
                            <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i>
                        </th>
                        <th>Unit Purchase Price <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                        <th>Billing Amount <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                        <th>Current stock <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                        <th>Item Type <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                        <th>Category <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                        <th>Brand <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                        <th>Tax <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
                        <th>Item Code <i class="bi bi-arrow-down-up" style="font-size:10px;opacity:.5"></i></th>
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
                            <div style="width:44px;height:44px;background:var(--body-bg);
                                    border:1px solid var(--border);border-radius:6px;
                                    display:flex;align-items:center;justify-content:center">
                                <i class="bi bi-image" style="font-size:18px;color:#cbd5e1"></i>
                            </div>
                        </td>
                        <td>
                            <div style="position:relative;display:inline-block">
                                <button type="button" class="actions-btn"
                                    onclick="toggleDropdown(this)"
                                    style="display:inline-flex;align-items:center;gap:5px;
                                           padding:5px 12px;border-radius:20px;
                                           border:1.5px solid var(--primary);
                                           background:#fff;color:var(--primary);
                                           font-size:12px;font-weight:600;cursor:pointer">
                                    Actions <i class="bi bi-chevron-down" style="font-size:10px"></i>
                                </button>
                                <div class="actions-dropdown"
                                    style="display:none;position:absolute;top:calc(100% + 4px);left:0;
                                        background:#fff;border:1px solid var(--border);
                                        border-radius:8px;box-shadow:var(--shadow-md);
                                        min-width:140px;z-index:200;overflow:hidden">
                                    <a href="{{ route('items.edit', $item) }}"
                                        style="display:flex;align-items:center;gap:8px;
                                          padding:9px 14px;font-size:13px;color:var(--text-primary);
                                          text-decoration:none;transition:background .15s"
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
                                                   background:transparent;border:none;cursor:pointer;
                                                   transition:background .15s;text-align:left"
                                            onmouseover="this.style.background='#fee2e2'"
                                            onmouseout="this.style.background='transparent'">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                        <td style="font-weight:600;font-size:13px;max-width:160px">{{ $item->item_name }}</td>
                        <td style="font-size:13px">SBS Shipping</td>
                        <td style="font-size:13px">TK. {{ number_format($item->exc_tax ?? 0, 2) }}</td>
                        <td style="font-size:13px">TK. {{ number_format($item->billing_exc_tax ?? 0, 2) }}</td>
                        <td style="font-size:13px;color:{{ ($item->current_stock ?? 0) < 0 ? '#dc2626' : '#0f172a' }};">
                            {{ number_format($item->current_stock ?? 0, 2) }} Nos
                        </td>
                        <td style="font-size:13px">{{ $item->item_type ?? 'Single' }}</td>
                        <td style="font-size:13px;color:var(--text-muted)">{{ $item->category ?? '' }}</td>
                        <td style="font-size:13px;color:var(--text-muted)">{{ $item->brand ?? '' }}</td>
                        <td style="font-size:13px;color:var(--text-muted)">{{ $item->applicable_tax == 'None' ? '' : $item->applicable_tax }}</td>
                        <td style="font-size:13px;font-weight:600;color:var(--primary)">{{ $item->item_code ?? '' }}</td>
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

        {{-- Footer: pagination info --}}
        @if($items->hasPages())
        <div class="pagination-wrapper" style="display:flex;align-items:center;justify-content:space-between">
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
</div>

@endsection

@push('styles')
<style>
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

    /* Filter panel toggle */
    #filterPanel.open {
        display: block !important;
    }

    /* Table sort arrows subtle */
    thead th {
        white-space: nowrap;
    }
</style>
@endpush

@push('scripts')
<script>
    // Select all checkboxes
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    });

    // Actions dropdown toggle
    function toggleDropdown(btn) {
        const dd = btn.nextElementSibling;
        // Close all others
        document.querySelectorAll('.actions-dropdown').forEach(d => {
            if (d !== dd) d.style.display = 'none';
        });
        dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
    }
    // Close dropdowns on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.actions-btn') && !e.target.closest('.actions-dropdown')) {
            document.querySelectorAll('.actions-dropdown').forEach(d => d.style.display = 'none');
        }
    });

    // Live search filter
    document.getElementById('liveSearch').addEventListener('input', function() {
        const val = this.value.toLowerCase();
        document.querySelectorAll('#itemsBody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
        });
    });
</script>
@endpush