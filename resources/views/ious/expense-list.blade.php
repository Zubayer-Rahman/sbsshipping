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
                    <th style="padding: 12px; text-align: left;">Date</th>
                    <th style="padding: 12px; text-align: left;">Type</th>
                    <th style="padding: 12px; text-align: left;">Status</th>
                    <th style="padding: 12px; text-align: left;">IOU Ref</th>
                    <th style="padding: 12px; text-align: left;">Contact</th>
                    <th style="padding: 12px; text-align: left;">Job ID</th>
                    <th style="padding: 12px; text-align: left;">Client</th>
                    <th style="padding: 12px; text-align: left;">Amount</th>
                    <th style="padding: 12px; text-align: left;">Method</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr style="border-top: 1px solid var(--border);">
                    <td style="padding: 12px;">{{ $payment->payment_date->format('d M Y') }}</td>
                    <td style="padding: 12px;">
                        <span class="badge {{ $payment->iou->type == 'receivable' ? 'badge-success' : 'badge-danger' }}">
                            {{ $payment->iou->type == 'receivable' ? 'Received' : 'Paid' }}
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        @if($payment->iou->status == 'paid')
                        <span class="badge badge-success">Paid</span>
                        @elseif($payment->iou->status == 'partial')
                        <span class="badge badge-warning">Partial</span>
                        @else
                        <span class="badge badge-secondary">Pending</span>
                        @endif
                    </td>
                    <td style="padding: 12px;"><a href="{{ route('ious.show', $payment->iou_id) }}" class="text-link">{{ $payment->iou->reference_number }}</a></td>
                    <td style="padding: 12px;">{{ $payment->iou->contact->name }}</td>
                    <td style="padding: 12px;">{{ $payment->job_id ?? '-' }}</td>
                    <td style="padding: 12px;">{{ $payment->client->name ?? '-' }}</td>
                    <td style="padding: 12px; font-weight: 700; color: var(--danger);">৳{{ number_format($payment->amount, 2) }}</td>
                    <td style="padding: 12px;">{{ $payment->payment_method }}</td>
                    <td class="text-center" style="padding: 12px;">
                        <a href="{{ route('ious.show', $payment->iou_id) }}" class="text-link">View IOU</a>
                    </td>
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

@push('styles')
<Style>
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-secondary {
        background: #e2e8f0;
        color: #475569;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }
</Style>

@endpush