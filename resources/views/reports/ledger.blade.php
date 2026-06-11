@extends('layouts.app')
@section('content')
<div style="padding: 2rem; font-family: 'Inter', sans-serif;">
    <div style="margin-bottom: 20px;">
        <a href="{{ url()->previous() }}" style="text-decoration: none; color: var(--primary); font-weight: 600;">← Back to Reports</a>
        <h1 style="font-size: 24px; font-weight: 800; margin-top: 10px;">Transaction Ledger: {{ $contact->business_name }}</h1>
    </div>

    <div style="background: #fff; border: 1px solid var(--border); border-radius: 12px; box-shadow: var(--shadow-sm);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--body-bg);">
                <tr>
                    <th style="padding: 12px 20px; text-align: left;">Date</th>
                    <th style="padding: 12px 20px; text-align: left;">Type</th>
                    <th style="padding: 12px 20px; text-align: left;">Reference</th>
                    <th style="padding: 12px 20px; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $t)
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 15px 20px;">{{ \Carbon\Carbon::parse($t['created_at'])->format('d M Y') }}</td>
                    <td style="padding: 15px 20px;">
                        <span style="padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #e0e7ff; color: #4338ca;">
                            {{ $t['t_type'] }}
                        </span>
                    </td>
                    <td style="padding: 15px 20px; font-weight: 600;">{{ $t['bill_no'] ?? $t['reference_no'] ?? $t['reference_number'] ?? '—' }}</td>
                    <td style="padding: 15px 20px; text-align: right; font-weight: 700;">
                        TK. {{ number_format($t['total_payable'] ?? $t['payment_amount'] ?? $t['amount'] ?? 0, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection