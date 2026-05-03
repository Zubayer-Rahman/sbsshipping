@extends('layouts.app')
@section('title','Edit IOU')
@section('page-title','Edit IOU')
@section('breadcrumb','IOUs / Edit IOU')

@section('content')
<div class="iou-container" style="padding: 2rem; font-family: 'Inter', sans-serif;">
    <div class="iou-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 class="iou-title" style="font-size: 2rem; font-weight: 700;">IOU Expense List</h1>
    </div>

    <div class="table-card" style="background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow-sm); overflow: hidden;">
        <table class="data-table" style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--body-bg);">
                <tr>
                    <th style="padding: 1rem; text-align: left;">Date</th>
                    <th style="padding: 1rem; text-align: left;">IOU Ref</th>
                    <th style="padding: 1rem; text-align: left;">Contact</th>
                    <th style="padding: 1rem; text-align: left;">Job ID</th>
                    <th style="padding: 1rem; text-align: left;">Client</th>
                    <th style="padding: 1rem; text-align: right;">Amount</th>
                    <th style="padding: 1rem; text-align: left;">Method</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr style="border-top: 1px solid var(--border);">
                    <td style="padding: 1rem;">{{ $payment->payment_date->format('d M Y') }}</td>
                    <td style="padding: 1rem;"><a href="{{ route('ious.show', $payment->iou_id) }}" class="text-link">{{ $payment->iou->reference_number }}</a></td>
                    <td style="padding: 1rem;">{{ $payment->iou->contact->name }}</td>
                    <td style="padding: 1rem;">{{ $payment->job_id ?? '-' }}</td>
                    <td style="padding: 1rem;">{{ $payment->client->name ?? '-' }}</td>
                    <td style="padding: 1rem; text-align: right; font-weight: 700; color: var(--danger);">৳{{ number_format($payment->amount, 2) }}</td>
                    <td style="padding: 1rem;">{{ $payment->payment_method }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 2rem; text-align: center; color: var(--text-muted);">No IOU expenses recorded.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top: 1rem;">{{ $payments->links() }}</div>
</div>
@endsection