@extends('layouts.app')
@section('title','Bill Details')
@section('page-title','Bill Details')
@section('breadcrumb','Bill / View Bill')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
    <h2 style="font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
        Bill Details
        <span style="font-size:15px;font-weight:500;color:var(--primary);margin-left:8px">(Bill No.: {{ $bill->bill_no }})</span>
    </h2>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a href="{{ route('bills.print', $bill) }}" target="_blank"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:8px;
                  background:#10b981;color:#fff;font-size:13px;font-weight:700;text-decoration:none">
            <i class="bi bi-printer"></i> Print Invoice
        </a>
        <a href="{{ route('bills.edit', $bill) }}"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:8px;
                  background:var(--primary);color:#fff;font-size:13px;font-weight:700;text-decoration:none">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('bills.list') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

{{-- ── Meta header ── --}}
<div class="card" style="margin-bottom:14px">
    <div class="card-body" style="padding:24px">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;flex-wrap:wrap">

            <div>
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--text-muted);letter-spacing:.06em;margin-bottom:6px">Client</div>
                <div style="font-weight:700;font-size:15px">{{ $bill->client_name ?? '—' }}</div>
                @if($bill->client_contact)
                <div style="font-size:13px;color:var(--text-muted);margin-top:2px">{{ $bill->client_contact }}</div>
                @endif
                @if($bill->billing_address)
                <div style="font-size:13px;color:var(--text-muted);margin-top:2px;line-height:1.5">{{ $bill->billing_address }}</div>
                @endif
            </div>

            <div>
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--text-muted);letter-spacing:.06em;margin-bottom:6px">Bill Info</div>
                <div style="display:flex;flex-direction:column;gap:5px;font-size:13px">
                    <div><span style="color:var(--text-muted);min-width:110px;display:inline-block">Bill No.:</span> <strong style="color:var(--primary)">#{{ $bill->bill_no }}</strong></div>
                    <div><span style="color:var(--text-muted);min-width:110px;display:inline-block">Date:</span> {{ $bill->billing_date->format('d/m/Y') }}</div>
                    <div><span style="color:var(--text-muted);min-width:110px;display:inline-block">Job Number:</span> {{ $bill->job_number ?? '—' }}</div>
                    <div><span style="color:var(--text-muted);min-width:110px;display:inline-block">Status:</span>
                        <span style="background:#d1fae5;color:#065f46;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700">{{ $bill->status }}</span>
                    </div>
                </div>
            </div>

            <div>
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:var(--text-muted);letter-spacing:.06em;margin-bottom:6px">Payment</div>
                @php $pc=['Paid'=>'background:#d1fae5;color:#065f46','Due'=>'background:#fef3c7;color:#92400e','Partial'=>'background:#dbeafe;color:#1e40af']; @endphp
                <div style="display:flex;flex-direction:column;gap:5px;font-size:13px">
                    <div><span style="color:var(--text-muted);min-width:110px;display:inline-block">Payment Status:</span>
                        <span style="{{ $pc[$bill->payment_status]??'' }};padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700">{{ $bill->payment_status }}</span>
                    </div>
                    <div><span style="color:var(--text-muted);min-width:110px;display:inline-block">Total Payable:</span> <strong>TK. {{ number_format($bill->total_payable,2) }}</strong></div>
                    <div><span style="color:var(--text-muted);min-width:110px;display:inline-block">Total Paid:</span> <span style="color:var(--success);font-weight:600">TK. {{ number_format($bill->total_paid,2) }}</span></div>
                    <div><span style="color:var(--text-muted);min-width:110px;display:inline-block">Remaining:</span> <span style="color:{{ $bill->total_remaining>0?'var(--danger)':'var(--success)' }};font-weight:700">TK. {{ number_format($bill->total_remaining,2) }}</span></div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ── Items table ── --}}
<div class="card" style="margin-bottom:14px">
    <div style="padding:16px 22px 12px;border-bottom:1px solid var(--border)">
        <span style="font-size:15px;font-weight:700;color:var(--text-primary)">Items:</span>
    </div>
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:#10b981;color:#fff">
                    <th style="padding:10px 14px;text-align:center;width:40px">#</th>
                    <th style="padding:10px 14px;text-align:left">Item</th>
                    <th style="padding:10px 14px;text-align:center">Quantity</th>
                    <th style="padding:10px 14px;text-align:right">Unit Price</th>
                    <th style="padding:10px 14px;text-align:right">Discount</th>
                    <th style="padding:10px 14px;text-align:right">Tax</th>
                    <th style="padding:10px 14px;text-align:right">Price inc. tax</th>
                    <th style="padding:10px 14px;text-align:right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bill->items as $i => $item)
                <tr style="border-bottom:1px solid var(--border)">
                    <td style="padding:10px 14px;text-align:center;color:var(--text-muted)">{{ $i+1 }}</td>
                    <td style="padding:10px 14px;font-weight:600">
                        {{ $item->item_name }}
                        @if($item->item_code) <span style="color:var(--text-muted);font-size:11px"> {{ $item->item_code }}</span>@endif
                        @if($item->description)<div style="font-size:12px;color:var(--text-muted)">{{ $item->description }}</div>@endif
                    </td>
                    <td style="padding:10px 14px;text-align:center">{{ number_format($item->quantity,2) }} {{ $item->unit ?? 'Nos' }}</td>
                    <td style="padding:10px 14px;text-align:right">TK. {{ number_format($item->unit_price,2) }}</td>
                    <td style="padding:10px 14px;text-align:right">TK. {{ number_format($item->discount,2) }}</td>
                    <td style="padding:10px 14px;text-align:right">TK. {{ number_format($item->tax,2) }}</td>
                    <td style="padding:10px 14px;text-align:right">TK. {{ number_format($item->price_inc_tax,2) }}</td>
                    <td style="padding:10px 14px;text-align:right;font-weight:700">TK. {{ number_format($item->subtotal,2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:24px;color:var(--text-muted)">No items</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Payment info + Summary ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">

    <div class="card">
        <div style="padding:16px 22px 12px;border-bottom:1px solid var(--border)">
            <span style="font-size:14px;font-weight:700">Payment info:</span>
        </div>
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:13px">
                <thead>
                    <tr style="background:#10b981;color:#fff">
                        <th style="padding:8px 12px">#</th>
                        <th style="padding:8px 12px">Date</th>
                        <th style="padding:8px 12px">Reference No</th>
                        <th style="padding:8px 12px;text-align:right">Amount</th>
                        <th style="padding:8px 12px">Payment mode</th>
                        <th style="padding:8px 12px">Payment note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bill->payments as $i => $pay)
                    <tr style="border-bottom:1px solid var(--border)">
                        <td style="padding:8px 12px">{{ $i+1 }}</td>
                        <td style="padding:8px 12px;white-space:nowrap">{{ $pay->paid_on->format('d/m/Y') }}</td>
                        <td style="padding:8px 12px;color:var(--primary);font-weight:600">{{ $pay->reference_no }}</td>
                        <td style="padding:8px 12px;text-align:right;font-weight:600">TK. {{ number_format($pay->amount,2) }}</td>
                        <td style="padding:8px 12px">{{ $pay->payment_method }}</td>
                        <td style="padding:8px 12px;color:var(--text-muted)">{{ $pay->payment_note ?: '--' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:20px;color:var(--text-muted)">No payments recorded</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Add payment form --}}
        <div style="padding:16px 22px;border-top:1px solid var(--border)">
            <div style="font-size:13px;font-weight:700;margin-bottom:12px;color:var(--text-primary)">Add Payment</div>
            <form method="POST" action="{{ route('bills.addPayment', $bill) }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px">
                    <div class="form-group">
                        <label class="form-label" style="font-size:12px">Amount</label>
                        <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:12px">Method</label>
                        <select name="payment_method" class="form-select">
                            @foreach(['Cash','Bank Transfer','Cheque','bKash','Nagad'] as $m)
                            <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:12px">Paid On</label>
                        <input type="datetime-local" name="paid_on" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="font-size:12px">Note</label>
                        <input type="text" name="payment_note" class="form-control" placeholder="Optional">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Add Payment</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div style="padding:22px">
            @foreach([
            ['Total:', 'TK. '.number_format($bill->sub_total,2), false],
            ['Discount:', '(-) '.number_format($bill->discount_amount,2).' %',false],
            ['Order Tax:', '(+) '.number_format($bill->order_tax_value,2), false],
            ['Shipping:', '(+) TK. '.number_format($bill->shipping_charges,2),false],
            ['Round Off:', 'TK. 0.00', false],
            ['Total Payable:', 'TK. '.number_format($bill->total_payable,2), true],
            ['Total paid:', 'TK. '.number_format($bill->total_paid,2), false],
            ['Total remaining:', 'TK. '.number_format($bill->total_remaining,2), false],
            ] as [$label,$value,$bold])
            <div style="display:flex;justify-content:space-between;padding:8px 0;
                        border-bottom:1px solid var(--border);font-size:13px;
                        {{ $bold ? 'font-weight:800;font-size:15px;border-bottom:none;margin-top:4px' : '' }}">
                <span style="color:{{ $bold ? 'var(--text-primary)' : 'var(--text-muted)' }}">{{ $label }}</span>
                <span style="color:{{ $bold ? 'var(--primary)' : 'var(--text-primary)' }}">{{ $value }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ── Notes + Activity ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
    <div class="card">
        <div style="padding:16px 22px 12px;border-bottom:1px solid var(--border)"><span style="font-size:14px;font-weight:700">Billing note:</span></div>
        <div style="padding:16px 22px;font-size:13px;color:var(--text-muted)">{{ $bill->billing_note ?: '--' }}</div>
    </div>
    <div class="card">
        <div style="padding:16px 22px 12px;border-bottom:1px solid var(--border)"><span style="font-size:14px;font-weight:700">Staff note:</span></div>
        <div style="padding:16px 22px;font-size:13px;color:var(--text-muted)">{{ $bill->staff_note ?: '--' }}</div>
    </div>
</div>

<div class="card" style="margin-bottom:20px">
    <div style="padding:16px 22px 12px;border-bottom:1px solid var(--border)"><span style="font-size:14px;font-weight:700">Activities:</span></div>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
            <tr style="background:#f0f4ff">
                <th style="padding:10px 14px;font-size:11px;text-transform:uppercase;color:var(--text-muted);font-weight:700">Date</th>
                <th style="padding:10px 14px;font-size:11px;text-transform:uppercase;color:var(--text-muted);font-weight:700">Action</th>
                <th style="padding:10px 14px;font-size:11px;text-transform:uppercase;color:var(--text-muted);font-weight:700">By</th>
                <th style="padding:10px 14px;font-size:11px;text-transform:uppercase;color:var(--text-muted);font-weight:700">Note</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding:10px 14px">{{ $bill->created_at->format('d/m/Y H:i') }}</td>
                <td style="padding:10px 14px">Added</td>
                <td style="padding:10px 14px">{{ $bill->added_by }}</td>
                <td style="padding:10px 14px">
                    Status: <span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700">{{ $bill->status }}</span>
                    Total: <span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700">TK. {{ number_format($bill->total_payable,2) }}</span>
                    Payment: <span style="{{ ['Paid'=>'background:#d1fae5;color:#065f46','Due'=>'background:#fef3c7;color:#92400e','Partial'=>'background:#dbeafe;color:#1e40af'][$bill->payment_status]??'' }};padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700">{{ $bill->payment_status }}</span>
                </td>
            </tr>
        </tbody>
    </table>
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
</style>
@endpush