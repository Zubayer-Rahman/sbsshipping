{{-- resources/views/contacts/index.blade.php --}}
@extends('layouts.app')

@section('title', $type === 'supplier' ? 'Suppliers' : 'Clients')
@section('page-title', $type === 'supplier' ? 'Suppliers' : 'Clients')
@section('breadcrumb', 'Contacts / ' . ($type === 'supplier' ? 'Suppliers' : 'Clients'))

@section('content')

<div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            All your {{ $type === 'supplier' ? 'Suppliers' : 'Clients' }}
        </h2>
        <a href="{{ route('contacts.create', ['type' => $type]) }}"
           class="btn"
           style="background:var(--primary);color:#fff;padding:10px 24px;
                  border-radius:25px;font-size:14px;font-weight:600;
                  display:inline-flex;align-items:center;gap:8px;
                  box-shadow:0 4px 14px rgba(59,130,246,.35);text-decoration:none">
            <span style="font-size:18px;font-weight:700">+</span> Add
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div style="background:#d4edda;color:#155724;padding:12px 20px;border-radius:6px;
                    margin-bottom:16px;border:1px solid #c3e6cb;display:flex;
                    justify-content:space-between;align-items:center">
            <span>{{ session('success') }}</span>
            <span onclick="this.parentElement.remove()" style="cursor:pointer;font-size:18px">&times;</span>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="card">
        <div class="card-body" style="padding:24px;overflow-x:auto">
            <table class="contacts-table" id="contactsTable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Contact ID</th>
                        <th>Business Name</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Tax Number</th>
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
                    <tr class="{{ !$contact->is_active ? 'row-inactive' : '' }}">
                        {{-- Action Dropdown --}}
                        <td>
                            <div class="action-dropdown">
                                <button class="action-btn" onclick="toggleDropdown(this)">
                                    Actions <span class="action-caret">▼</span>
                                </button>
                                <div class="action-menu">
                                    <a href="{{ route('contacts.show', $contact) }}">
                                        <span style="color:#17a2b8">&#128065;</span> View
                                    </a>
                                    <a href="{{ route('contacts.edit', $contact) }}">
                                        <span style="color:var(--primary)">&#9998;</span> Edit
                                    </a>
                                    <form action="{{ route('contacts.destroy', $contact) }}" method="POST"
                                          onsubmit="return confirm('Delete this contact?')">
                                        @csrf @method('DELETE')
                                        <button type="submit">
                                            <span style="color:var(--danger)">&#128465;</span> Delete
                                        </button>
                                    </form>
                                    <form action="{{ route('contacts.toggle', $contact) }}" method="POST">
                                        @csrf
                                        <button type="submit">
                                            <span style="color:#ffc107">&#9889;</span>
                                            {{ $contact->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                        <td>{{ $contact->contact_id }}</td>
                        <td>{{ $contact->business_name }}</td>
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ $contact->tax_number }}</td>
                        <td>{{ $contact->pay_term_display }}</td>
                        <td>TK. {{ number_format($contact->opening_balance, 2) }}</td>
                        <td>TK. {{ number_format($contact->advance_balance, 2) }}</td>
                        <td>{{ $contact->created_at->format('d/m/Y') }}</td>
                        <td>{{ $contact->address }}</td>
                        <td>{{ $contact->mobile }}</td>
                        <td>TK. {{ number_format($contact->total_purchase_due, 2) }}</td>
                        <td>TK. {{ number_format($contact->total_purchase_return_due, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" style="text-align:center;padding:40px;color:#999">
                            No {{ $type === 'supplier' ? 'suppliers' : 'clients' }} found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .contacts-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .contacts-table thead th {
        background: #f0f3f8;
        color: var(--text-primary);
        font-weight: 700;
        font-size: 12px;
        padding: 12px 10px;
        border-bottom: 2px solid var(--border);
        white-space: nowrap;
        text-align: left;
    }
    .contacts-table tbody td {
        padding: 10px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
        white-space: nowrap;
    }
    .contacts-table tbody tr:hover {
        background: #f8f9ff;
    }
    .row-inactive {
        opacity: 0.5;
        background: #f5f5f5 !important;
    }

    /* Action Dropdown */
    .action-dropdown {
        position: relative;
        display: inline-block;
    }
    .action-btn {
        background: #28a745;
        color: #fff;
        border: none;
        padding: 5px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background .2s;
    }
    .action-btn:hover {
        background: #218838;
    }
    .action-caret {
        font-size: 9px;
    }
    .action-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background: var(--card-bg, #fff);
        border: 1px solid var(--border);
        border-radius: 8px;
        box-shadow: 0 6px 20px rgba(0,0,0,.12);
        min-width: 160px;
        z-index: 100;
        padding: 6px 0;
        margin-top: 4px;
    }
    .action-menu.show {
        display: block;
    }
    .action-menu a,
    .action-menu button {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        font-size: 13px;
        color: var(--text-primary);
        text-decoration: none;
        background: none;
        border: none;
        width: 100%;
        cursor: pointer;
        transition: background .15s;
    }
    .action-menu a:hover,
    .action-menu button:hover {
        background: #f0f3f8;
    }
    .action-menu form {
        margin: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    function toggleDropdown(btn) {
        // Close all other open menus
        document.querySelectorAll('.action-menu.show').forEach(m => {
            if (m !== btn.nextElementSibling) m.classList.remove('show');
        });
        btn.nextElementSibling.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.action-dropdown')) {
            document.querySelectorAll('.action-menu.show').forEach(m => m.classList.remove('show'));
        }
    });
</script>
@endpush