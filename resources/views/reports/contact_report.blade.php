@extends('layouts.app')
@section('content')

<div style="padding: 2rem; font-family: 'Inter', sans-serif;">
    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="font-size: 22px; font-weight: 800; color: var(--text-primary);">{{ $title }}</h1>
    </div>

    {{-- Tabs --}}
    <div style="display: flex; gap: 5px; margin-bottom: -1px;">
        <a href="{{ route('reports.clients') }}" style="padding: 10px 20px; border-radius: 8px 8px 0 0; text-decoration: none; font-weight: 600; font-size: 14px; {{ $type == 'client' ? 'background: #fff; border: 1px solid var(--border); border-bottom: 2px solid #fff; color: var(--primary);' : 'background: var(--body-bg); color: var(--text-muted);' }}">Clients</a>
        <a href="{{ route('reports.suppliers') }}" style="padding: 10px 20px; border-radius: 8px 8px 0 0; text-decoration: none; font-weight: 600; font-size: 14px; {{ $type == 'supplier' ? 'background: #fff; border: 1px solid var(--border); border-bottom: 2px solid #fff; color: var(--primary);' : 'background: var(--body-bg); color: var(--text-muted);' }}">Suppliers</a>
    </div>

    <div style="background: #fff; border: 1px solid var(--border); border-radius: 0 12px 12px 12px; box-shadow: var(--shadow-sm); overflow: hidden;">
        {{-- Toolbar --}}
        <div style="padding: 15px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: #fafbfc;">
            <button class="btn-export">Export Excel</button>
            <button class="btn-export">Print</button>
            <input type="text" placeholder="Search..." style="padding: 6px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 13px;">
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8fafc;">
                <tr>
                    <th style="padding: 12px 20px; text-align: left; font-size: 11px; text-transform: uppercase; color: var(--text-muted);">Contact</th>
                    @if($type == 'client')
                    <th style="padding: 12px 20px; text-align: right; font-size: 11px; text-transform: uppercase; color: var(--text-muted);">Total Sale</th>
                    <th style="padding: 12px 20px; text-align: right; font-size: 11px; text-transform: uppercase; color: var(--text-muted);">Total Paid</th>
                    <th style="padding: 12px 20px; text-align: right; font-size: 11px; text-transform: uppercase; color: var(--text-muted);">Due Balance</th>
                    @else
                    <th style="padding: 12px 20px; text-align: right; font-size: 11px; text-transform: uppercase; color: var(--text-muted);">Total Purchase</th>
                    @endif
                    <th style="padding: 12px 20px; text-align: center; font-size: 11px; text-transform: uppercase; color: var(--text-muted);">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contacts as $contact)
                <tr style="border-bottom: 1px solid var(--border); transition: 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 15px 20px;">
                        <a href="{{ route('reports.contact.ledger', $contact->id) }}" style="text-decoration: none; color: var(--primary); font-weight: 700; font-size: 14px;">
                            {{ $contact->business_name }}
                        </a>
                        <div style="font-size: 11px; color: var(--text-muted);">{{ $contact->contact_id }}</div>
                    </td>

                    @if($type == 'client')
                    <td style="padding: 15px 20px; text-align: right; font-weight: 600;">TK. {{ number_format($contact->total_billed, 2) }}</td>
                    <td style="padding: 15px 20px; text-align: right; color: var(--success); font-weight: 600;">TK. {{ number_format($contact->total_paid, 2) }}</td>
                    <td style="padding: 15px 20px; text-align: right; color: var(--danger); font-weight: 700;">TK. {{ number_format($contact->total_billed - $contact->total_paid, 2) }}</td>
                    @else
                    <td style="padding: 15px 20px; text-align: right; font-weight: 600;">TK. {{ number_format($contact->total_purchased, 2) }}</td>
                    @endif

                    <td style="padding: 15px 20px; text-align: center;">
                        <a href="{{ route('reports.contact.ledger', $contact->id) }}" class="btn-view">View Ledger</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .btn-export {
        background: #fff;
        border: 1px solid var(--border);
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        color: var(--text-muted);
        transition: 0.2s;
    }

    .btn-export:hover {
        background: var(--body-bg);
        color: var(--text-primary);
    }

    .btn-view {
        background: var(--primary-light);
        color: var(--primary);
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }
</style>
@endsection