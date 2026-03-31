@extends('layouts.app')

@section('title', 'List Items')
@section('page-title', 'List Items')
@section('breadcrumb', 'Items / List Items')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
    <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;
               color:var(--text-primary);text-transform:uppercase;letter-spacing:.04em">
        LIST ITEMS
    </h2>
    <a href="{{ route('items.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Item
    </a>
</div>

{{-- Search --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 22px">
        <form method="GET" action="{{ route('items.list') }}"
            style="display:flex;gap:10px;align-items:center">
            <div style="position:relative;flex:1;max-width:380px">
                <i class="bi bi-search" style="position:absolute;left:12px;top:50%;
                   transform:translateY(-50%);color:var(--text-muted);font-size:14px"></i>
                <input type="text" name="search" class="form-control"
                    placeholder="Search by item name or code..."
                    style="padding-left:36px"
                    value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('search'))
            <a href="{{ route('items.list') }}" class="btn btn-outline">Clear</a>
            @endif
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">SL.</th>
                    <th>Item Name</th>
                    <th>Item Code</th>
                    <th>Unit</th>
                    <th>Category</th>
                    <th>Tax</th>
                    <th style="text-align:right">Purchase Price (Exc.)</th>
                    <th style="text-align:right">Margin %</th>
                    <th style="text-align:right">Billing Amount</th>
                    <th style="text-align:center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td style="color:var(--text-muted);font-size:13px">
                        {{ $items->total() - (($items->currentPage()-1) * $items->perPage()) - $loop->index }}
                    </td>
                    <td style="font-weight:600;font-size:13.5px">{{ $item->item_name }}</td>
                    <td style="font-size:13px;color:var(--text-muted)">{{ $item->item_code ?? '—' }}</td>
                    <td style="font-size:13px">{{ $item->unit ?? '—' }}</td>
                    <td style="font-size:13px">{{ $item->item_type ?? '—' }}</td>
                    <td style="font-size:13px">
                        <span style="display:inline-block;padding:2px 9px;border-radius:20px;
                                     font-size:11px;font-weight:700;
                                     @if($item->applicable_tax=='None')
                                       background:#e2e8f0; color:#475569;
                                     @else
                                       background:#dbeafe; color:#1e40af;
                                     @endif">
                            {{ $item->applicable_tax ?? 'None' }}
                        </span>
                    </td>
                    <td style="text-align:right;font-size:13px;font-weight:500">
                        {{ number_format($item->exc_tax ?? 0, 2) }}
                    </td>
                    <td style="text-align:right;font-size:13px">
                        {{ number_format($item->margin ?? 0, 2) }}%
                    </td>
                    <td style="text-align:right;font-size:13px;font-weight:700;color:var(--primary)">
                        {{ number_format($item->billing_exc_tax ?? 0, 2) }}
                    </td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center">
                            <a href="{{ route('items.edit', $item) }}"
                                style="display:inline-flex;align-items:center;gap:4px;
                                      padding:5px 12px;border-radius:5px;
                                      background:var(--primary);color:#fff;
                                      font-size:12px;font-weight:600;text-decoration:none;
                                      transition:background .15s"
                                onmouseover="this.style.background='var(--primary-dark)'"
                                onmouseout="this.style.background='var(--primary)'">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('items.destroy', $item) }}"
                                onsubmit="return confirm('Delete this item?')" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    style="display:inline-flex;align-items:center;gap:4px;
                                               padding:5px 12px;border-radius:5px;
                                               background:var(--danger);color:#fff;border:none;
                                               font-size:12px;font-weight:600;cursor:pointer;
                                               transition:background .15s"
                                    onmouseover="this.style.background='#dc2626'"
                                    onmouseout="this.style.background='var(--danger)'">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:48px;color:var(--text-muted)">
                        <i class="bi bi-box-seam" style="font-size:40px;display:block;margin-bottom:10px;opacity:.3"></i>
                        No items found.
                        <a href="{{ route('items.create') }}" style="color:var(--primary)">Add your first item →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
    <div class="pagination-wrapper" style="display:flex;align-items:center;justify-content:space-between">
        <div style="font-size:13px;color:var(--text-muted)">
            Showing {{ $items->firstItem() }}–{{ $items->lastItem() }} of {{ $items->total() }} items
        </div>
        {{ $items->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
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
</style>
@endpush