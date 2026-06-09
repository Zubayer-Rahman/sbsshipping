@extends('layouts.app')
@section('title', 'Additional Expenses')
@section('content')

<div class="container" style="padding:2rem;font-family:'Inter',sans-serif">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
        <h1 style="font-size:1.75rem;font-weight:800;color:var(--text-primary);margin:0">
            Additional Expenses
        </h1>
        <a href="{{ route('bills.list') }}" class="btn btn-outline-secondary">← Back to Bills</a>
    </div>

    {{-- Stats Cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px">
        <div style="background:#fff;padding:18px;border-radius:10px;border-left:4px solid #1a56db;box-shadow:var(--shadow-sm)">
            <div style="font-size:12px;color:var(--text-muted);font-weight:600;text-transform:uppercase">Total All</div>
            <div style="font-size:22px;font-weight:800;color:#1a56db;margin-top:4px">৳ {{ number_format($totalAll, 2) }}</div>
        </div>
        <div style="background:#fff;padding:18px;border-radius:10px;border-left:4px solid #f59e0b;box-shadow:var(--shadow-sm)">
            <div style="font-size:12px;color:var(--text-muted);font-weight:600;text-transform:uppercase">⚡ Auto Charges</div>
            <div style="font-size:22px;font-weight:800;color:#f59e0b;margin-top:4px">৳ {{ number_format($totalAuto, 2) }}</div>
        </div>
        <div style="background:#fff;padding:18px;border-radius:10px;border-left:4px solid #8b5cf6;box-shadow:var(--shadow-sm)">
            <div style="font-size:12px;color:var(--text-muted);font-weight:600;text-transform:uppercase">✋ Manual</div>
            <div style="font-size:22px;font-weight:800;color:#8b5cf6;margin-top:4px">৳ {{ number_format($totalManual, 2) }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" style="background:#fff;padding:16px;border-radius:10px;margin-bottom:16px;
                              display:flex;gap:12px;box-shadow:var(--shadow-sm)">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search by description, bill no, or client..."
            style="flex:1;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
        <select name="type" style="padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
            <option value="">All Types</option>
            <option value="auto" {{ request('type')=='auto' ? 'selected' : '' }}>⚡ Auto Only</option>
            <option value="manual" {{ request('type')=='manual' ? 'selected' : '' }}>✋ Manual Only</option>
        </select>
        <button type="submit" style="padding:10px 20px;background:#1a56db;color:#fff;border:none;
                                     border-radius:6px;font-weight:600;cursor:pointer">Filter</button>
        <a href="{{ route('expenses.additionalExpenses') }}"
            style="padding:10px 20px;background:#64748b;color:#fff;border:none;border-radius:6px;
                  font-weight:600;text-decoration:none">Reset</a>
    </form>

    {{-- Expenses Table --}}
    <div style="background:#fff;border-radius:10px;overflow:hidden;box-shadow:var(--shadow-sm)">
        <table style="width:100%;border-collapse:collapse">
            <thead style="background:var(--body-bg)">
                <tr>
                    <th style="padding:14px;text-align:left;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Bill #</th>
                    <th style="padding:14px;text-align:left;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Client</th>
                    <th style="padding:14px;text-align:left;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Description</th>
                    <th style="padding:14px;text-align:center;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Type</th>
                    <th style="padding:14px;text-align:right;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Amount</th>
                    <th style="padding:14px;text-align:left;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr style="border-bottom:1px solid var(--border);transition:background .15s"
                    onmouseover="this.style.background='var(--primary-light)'"
                    onmouseout="this.style.background=''">
                    <td style="padding:14px;font-size:13px">
                        @if($expense->bill)
                        <a href="{{ route('bills.show', $expense->bill_id) }}"
                            style="color:#1a56db;font-weight:700;text-decoration:none">
                            #{{ $expense->bill->bill_no }}
                        </a>
                        @else
                        <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td style="padding:14px;font-size:13px">
                        {{ $expense->bill->client_name ?? '—' }}
                    </td>
                    <td style="padding:14px;font-size:13px;color:var(--text-primary);font-weight:500">
                        {{ $expense->description }}
                    </td>
                    <td style="padding:14px;text-align:center">
                        @if($expense->is_auto)
                        <span style="padding:4px 10px;border-radius:12px;background:#fef3c7;
                                         color:#92400e;font-size:11px;font-weight:700;text-transform:uppercase">
                            ⚡ Auto
                        </span>
                        @else
                        <span style="padding:4px 10px;border-radius:12px;background:#e0e7ff;
                                         color:#4338ca;font-size:11px;font-weight:700;text-transform:uppercase">
                            ✋ Manual
                        </span>
                        @endif
                    </td>
                    <td style="padding:14px;text-align:right;font-size:14px;font-weight:700;color:var(--text-primary)">
                        ৳ {{ number_format($expense->amount, 2) }}
                    </td>
                    <td style="padding:14px;font-size:12px;color:var(--text-muted)">
                        {{ $expense->created_at->format('d M Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:40px;text-align:center;color:var(--text-muted)">
                        No additional expenses found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px">{{ $expenses->withQueryString()->links() }}</div>

</div>
@endsection