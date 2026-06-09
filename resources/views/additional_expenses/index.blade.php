@extends('layouts.app')
@section('title', 'Additional Expenses')
@section('content')

<div style="padding:2rem;font-family:'Inter',sans-serif;max-width:1400px;margin:0 auto">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <div>
            <h1 style="font-size:1.875rem;font-weight:800;color:var(--text-primary);margin:0">
                Additional Expenses
            </h1>
            <p style="color:var(--text-muted);font-size:14px;margin:4px 0 0">
                Track extra job-related expenses and auto-add to bills
            </p>
        </div>
        <a href="{{ route('additional-expenses.create') }}"
            style="background:var(--primary);color:#fff;padding:10px 20px;border-radius:8px;
                  text-decoration:none;font-weight:600;font-size:14px">
            + Add Expense
        </a>
    </div>

    @if(session('success'))
    <div style="padding:12px 16px;background:#d1fae5;color:#065f46;border-radius:8px;margin-bottom:16px">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div style="padding:12px 16px;background:#fee2e2;color:#991b1b;border-radius:8px;margin-bottom:16px">
        {{ session('error') }}
    </div>
    @endif

    {{-- Stats Cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px">
        <div style="background:#fff;padding:20px;border-radius:12px;border-left:4px solid #ef4444;box-shadow:var(--shadow-sm)">
            <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.05em">
                Total Actual Spent
            </div>
            <div style="font-size:24px;font-weight:800;color:#ef4444;margin-top:6px">
                ৳ {{ number_format($totalActual, 2) }}
            </div>
        </div>
        <div style="background:#fff;padding:20px;border-radius:12px;border-left:4px solid #f59e0b;box-shadow:var(--shadow-sm)">
            <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.05em">
                Pending to Bill
            </div>
            <div style="font-size:24px;font-weight:800;color:#f59e0b;margin-top:6px">
                ৳ {{ number_format($totalToBill, 2) }}
            </div>
        </div>
        <div style="background:#fff;padding:20px;border-radius:12px;border-left:4px solid #10b981;box-shadow:var(--shadow-sm)">
            <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.05em">
                Already Billed
            </div>
            <div style="font-size:24px;font-weight:800;color:#10b981;margin-top:6px">
                ৳ {{ number_format($totalBilled, 2) }}
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" style="background:#fff;padding:16px;border-radius:10px;margin-bottom:16px;
                              display:flex;gap:12px;box-shadow:var(--shadow-sm)">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search by description, reference, or client..."
            style="flex:1;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
        <select name="status" style="padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
            <option value="">All Status</option>
            <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
            <option value="billed" {{ request('status')=='billed' ? 'selected' : '' }}>Billed</option>
            <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <button type="submit" style="padding:10px 20px;background:var(--primary);color:#fff;border:none;
                                     border-radius:6px;font-weight:600;cursor:pointer">Filter</button>
        <a href="{{ route('additional-expenses.index') }}"
            style="padding:10px 20px;background:#64748b;color:#fff;border-radius:6px;
                  font-weight:600;text-decoration:none">Reset</a>
    </form>

    {{-- Table --}}
    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:var(--shadow-sm)">
        <table style="width:100%;border-collapse:collapse">
            <thead style="background:var(--body-bg)">
                <tr>
                    <th style="padding:14px;text-align:left;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Ref #</th>
                    <th style="padding:14px;text-align:left;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Client</th>
                    <th style="padding:14px;text-align:left;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Job</th>
                    <th style="padding:14px;text-align:left;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Description</th>
                    <th style="padding:14px;text-align:right;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Actual</th>
                    <th style="padding:14px;text-align:right;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">To Bill</th>
                    <th style="padding:14px;text-align:right;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Margin</th>
                    <th style="padding:14px;text-align:center;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Status</th>
                    <th style="padding:14px;text-align:center;font-size:11px;color:var(--text-muted);
                               text-transform:uppercase;border-bottom:1px solid var(--border)">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $exp)
                <tr style="border-bottom:1px solid var(--border);transition:background .15s"
                    onmouseover="this.style.background='var(--primary-light)'"
                    onmouseout="this.style.background=''">
                    <td style="padding:14px;font-size:13px;font-weight:700;color:var(--primary)">
                        {{ $exp->reference_no }}
                    </td>
                    <td style="padding:14px;font-size:13px">
                        {{ $exp->client->business_name ?? '—' }}
                    </td>
                    <td style="padding:14px;font-size:13px">
                        @if($exp->job)
                        <span style="padding:3px 8px;background:var(--primary-light);color:var(--primary);
                                         border-radius:6px;font-size:11px;font-weight:600">
                            {{ $exp->job->job_id ?? 'Job #'.$exp->job_id }}
                        </span>
                        @else
                        <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td style="padding:14px;font-size:13px;color:var(--text-primary)">
                        {{ Str::limit($exp->description, 40) }}
                    </td>
                    <td style="padding:14px;text-align:right;font-size:13px;font-weight:600;color:#ef4444">
                        ৳ {{ number_format($exp->actual_amount, 2) }}
                    </td>
                    <td style="padding:14px;text-align:right;font-size:13px;font-weight:600;color:#1a56db">
                        ৳ {{ number_format($exp->to_be_billed, 2) }}
                    </td>
                    <td style="padding:14px;text-align:right;font-size:13px;font-weight:700;
                               color:{{ $exp->margin >= 0 ? '#10b981' : '#ef4444' }}">
                        {{ $exp->margin >= 0 ? '+' : '' }}৳ {{ number_format($exp->margin, 2) }}
                    </td>
                    <td style="padding:14px;text-align:center">
                        @if($exp->status === 'pending')
                        <span style="padding:4px 10px;border-radius:12px;background:#fef3c7;color:#92400e;
                                         font-size:11px;font-weight:700;text-transform:uppercase">Pending</span>
                        @elseif($exp->status === 'billed')
                        <span style="padding:4px 10px;border-radius:12px;background:#d1fae5;color:#065f46;
                                         font-size:11px;font-weight:700;text-transform:uppercase">Billed</span>
                        @else
                        <span style="padding:4px 10px;border-radius:12px;background:#fee2e2;color:#991b1b;
                                         font-size:11px;font-weight:700;text-transform:uppercase">Cancelled</span>
                        @endif
                    </td>
                    <td style="padding:14px;text-align:center">
                        <a href="{{ route('additional-expenses.show', $exp) }}"
                            style="color:var(--primary);text-decoration:none;font-size:12px;margin-right:8px">View</a>
                        @if($exp->status !== 'billed')
                        <a href="{{ route('additional-expenses.edit', $exp) }}"
                            style="color:var(--success);text-decoration:none;font-size:12px;margin-right:8px">Edit</a>
                        <form action="{{ route('additional-expenses.destroy', $exp) }}" method="POST"
                            style="display:inline" onsubmit="return confirm('Delete this expense?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                style="background:none;border:none;color:var(--danger);cursor:pointer;font-size:12px">
                                Delete
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="padding:40px;text-align:center;color:var(--text-muted)">
                        No additional expenses found. <a href="{{ route('additional-expenses.create') }}" style="color:var(--primary)">Create one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px">{{ $expenses->withQueryString()->links() }}</div>

</div>
@endsection