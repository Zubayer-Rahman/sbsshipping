@extends('layouts.app')

@section('title', $type === 'supplier' ? 'Suppliers' : 'Clients')
@section('page-title', $type === 'supplier' ? 'Suppliers' : 'Clients')
@section('breadcrumb', 'Contacts / ' . ($type === 'supplier' ? 'Suppliers' : 'Clients'))

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px">
    <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
        All your {{ $type === 'supplier' ? 'Suppliers' : 'Clients' }}
    </h2>
    <a href="{{ route('contacts.create', ['type' => $type]) }}"
        style="background:var(--primary);color:#fff;padding:10px 24px;
              border-radius:25px;font-size:14px;font-weight:600;
              display:inline-flex;align-items:center;gap:8px;
              box-shadow:0 4px 14px rgba(26,86,219,.3);text-decoration:none;white-space:nowrap">
        <span style="font-size:18px;font-weight:700;line-height:1">+</span> Add
    </a>
</div>

@if(session('success'))
<div style="background:#d1fae5;color:#065f46;padding:12px 20px;border-radius:8px;
            margin-bottom:16px;border:1px solid #a7f3d0;display:flex;
            justify-content:space-between;align-items:center">
    <span>{{ session('success') }}</span>
    <span onclick="this.parentElement.remove()" style="cursor:pointer;font-size:18px;line-height:1">&times;</span>
</div>
@endif

<div class="card" style="overflow:hidden">
    {{-- Scrollable table --}}
    <div class="ct-scroll">
        <table class="ct-table">
            <thead>
                <tr>
                    <th class="ct-sticky">Action</th>
                    <th>Contact ID</th>
                    <th>Business Name</th>
                    <th>Pay Term</th>
                    <th>Opening Balance</th>
                    <th>Advance Balance</th>
                    <th>Added On</th>
                    <th>Address</th>
                    <th>Mobile</th>
                    <th>Total Purchase Due</th>
                    <th>Purchase Return Due</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contacts as $contact)
                <tr class="{{ !$contact->is_active ? 'ct-inactive' : '' }}">
                    {{-- Sticky action cell --}}
                    <td class="ct-sticky ct-action-cell">
                        <div class="ct-dropdown">
                            <button class="ct-action-btn" type="button"
                                onclick="ctToggle(this)">
                                Actions <span style="font-size:9px">▼</span>
                            </button>
                            <div class="ct-menu">
                                <a href="{{ route('contacts.show', $contact) }}" class="ct-menu-item">
                                    <i class="bi bi-eye" style="color:#17a2b8"></i> View
                                </a>
                                <a href="{{ route('contacts.edit', $contact) }}" class="ct-menu-item">
                                    <i class="bi bi-pencil" style="color:var(--primary)"></i> Edit
                                </a>
                                <form action="{{ route('contacts.destroy', $contact) }}" method="POST"
                                    onsubmit="return confirm('Delete this contact?')" style="margin:0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ct-menu-item ct-menu-btn">
                                        <i class="bi bi-trash" style="color:var(--danger)"></i> Delete
                                    </button>
                                </form>
                                <form action="{{ route('contacts.toggle', $contact) }}" method="POST" style="margin:0">
                                    @csrf
                                    <button type="submit" class="ct-menu-item ct-menu-btn">
                                        <i class="bi bi-lightning" style="color:#f59e0b"></i>
                                        {{ $contact->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                    <td>{{ $contact->contact_id }}</td>
                    <td style="font-weight:600">{{ $contact->business_name }}</td>
                    <td>{{ $contact->pay_term_display }}</td>
                    <td>TK. {{ number_format($contact->opening_balance, 2) }}</td>
                    <td>TK. {{ number_format($contact->advance_balance, 2) }}</td>
                    <td style="white-space:nowrap">{{ $contact->created_at->format('d/m/Y') }}</td>
                    <td>{{ $contact->address }}</td>
                    <td style="white-space:nowrap">{{ $contact->mobile }}</td>
                    <td>TK. {{ number_format($contact->total_purchase_due, 2) }}</td>
                    <td>TK. {{ number_format($contact->total_purchase_return_due, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="text-align:center;padding:48px;color:var(--text-muted)">
                        <i class="bi bi-people" style="font-size:40px;display:block;margin-bottom:10px;opacity:.3"></i>
                        No {{ $type === 'supplier' ? 'suppliers' : 'clients' }} found.
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
    /* ── Scroll wrapper ── */
    .ct-scroll {
        width: 100%;
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
    }

    .ct-scroll::-webkit-scrollbar {
        height: 6px;
    }

    .ct-scroll::-webkit-scrollbar-track {
        background: #f0f0f0;
        border-radius: 3px;
    }

    .ct-scroll::-webkit-scrollbar-thumb {
        background: #c0c0c0;
        border-radius: 3px;
    }

    .ct-scroll::-webkit-scrollbar-thumb:hover {
        background: #999;
    }

    /* ── Table ── */
    .ct-table {
        width: 100%;
        min-width: 1000px;
        border-collapse: collapse;
        font-size: 13px;
    }

    .ct-table thead th {
        background: #f0f4ff;
        color: var(--text-primary);
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 11px 12px;
        border-bottom: 2px solid var(--border);
        white-space: nowrap;
        text-align: left;
    }

    .ct-table tbody td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
        color: var(--text-primary);
    }

    .ct-table tbody tr:last-child td {
        border-bottom: none;
    }

    .ct-table tbody tr:hover td {
        background: #f8faff;
    }

    .ct-inactive td {
        opacity: .5;
    }

    /* ── Sticky action column ── */
    .ct-sticky {
        position: sticky;
        left: 0;
        z-index: 3;
        background: #f0f4ff;
        /* thead */
    }

    .ct-action-cell {
        background: #fff;
        /* tbody */
        /* right-side shadow to visually separate from scrolling content */
        box-shadow: 3px 0 8px -2px rgba(15, 31, 75, .10);
    }

    /* Keep hover consistent */
    .ct-table tbody tr:hover .ct-action-cell {
        background: #f8faff;
    }

    /* ── Action button ── */
    .ct-action-btn {
        background: #28a745;
        color: #fff;
        border: none;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
        transition: background .2s;
    }

    .ct-action-btn:hover {
        background: #218838;
    }

    /* ── Dropdown wrapper ── */
    .ct-dropdown {
        position: relative;
        display: inline-block;
    }

    /* The .ct-menu divs inside the table are hidden — only the floating body menu shows */
    .ct-menu {
        display: none !important;
    }

    /* ── Floating menu items (rendered on body) ── */
    #ct-floating-menu .ct-menu-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        font-size: 13px;
        color: var(--text-primary);
        text-decoration: none;
        transition: background .15s;
        white-space: nowrap;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    #ct-floating-menu .ct-menu-item:hover {
        background: #f0f4ff;
    }

    #ct-floating-menu .ct-menu-btn {
        background: none;
        border: none;
        width: 100%;
        cursor: pointer;
        text-align: left;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    #ct-floating-menu form {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-primary);
        text-decoration: none;
        transition: background .15s;
        white-space: nowrap;
    }

    .ct-menu-item:hover {
        background: #f0f4ff;
    }

    .ct-menu-btn {
        background: none;
        border: none;
        width: 100%;
        cursor: pointer;
        text-align: left;
        font-family: inherit;
    }
</style>
@endpush

@push('scripts')
<script>
    // One shared floating menu appended to <body> — never clipped by table/sticky/overflow
    const floatingMenu = document.createElement('div');
    floatingMenu.id = 'ct-floating-menu';
    floatingMenu.style.cssText = `
    display:none;
    position:fixed;
    background:#fff;
    border:1px solid #e2e8f0;
    border-radius:8px;
    box-shadow:0 8px 24px rgba(15,31,75,.16);
    min-width:160px;
    z-index:99999;
    padding:6px 0;
`;
    document.body.appendChild(floatingMenu);

    let _activeBtn = null;

    function ctToggle(btn) {
        // Clicking same button → close
        if (_activeBtn === btn) {
            ctCloseAll();
            return;
        }
        ctCloseAll();

        // Clone this button's sibling menu content into the floating menu
        const source = btn.nextElementSibling;
        floatingMenu.innerHTML = source.innerHTML;
        floatingMenu.style.display = 'block';

        // Position it
        ctPosition(btn);

        _activeBtn = btn;
    }

    function ctPosition(btn) {
        const rect = btn.getBoundingClientRect();
        const menuW = 165;
        const menuH = floatingMenu.offsetHeight || 130;
        const spaceBelow = window.innerHeight - rect.bottom;

        // Vertical
        floatingMenu.style.top = spaceBelow < menuH + 8 ?
            (rect.top - menuH - 4) + 'px' :
            (rect.bottom + 4) + 'px';

        // Horizontal
        floatingMenu.style.left = (rect.left + menuW > window.innerWidth - 8) ?
            (rect.right - menuW) + 'px' :
            rect.left + 'px';
    }

    function ctCloseAll() {
        floatingMenu.style.display = 'none';
        floatingMenu.innerHTML = '';
        _activeBtn = null;
    }

    // Close on outside click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.ct-dropdown') && e.target !== floatingMenu && !floatingMenu.contains(e.target)) {
            ctCloseAll();
        }
    });

    // Reposition on scroll / resize
    ['scroll', 'resize'].forEach(evt =>
        window.addEventListener(evt, function() {
            if (_activeBtn) ctPosition(_activeBtn);
        }, true)
    );
</script>
@endpush