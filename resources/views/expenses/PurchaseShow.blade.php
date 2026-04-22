<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Purchase Details — {{ $purchase->reference_no }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif
        }

        body {
            background: #f0f4ff;
            padding: 20px
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px
        }

        .toolbar-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all .2s
        }

        .btn-print {
            background: #10b981;
            color: #fff
        }

        .btn-close {
            background: #e2e8f0;
            color: #475569
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 4px rgba(15, 31, 75, .08);
            margin-bottom: 14px
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 8px 20px;
            margin-bottom: 16px
        }

        .meta-item {
            font-size: 13px
        }

        .meta-label {
            color: #64748b;
            font-size: 12px
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px
        }

        thead th {
            background: #28a745;
            color: #fff;
            padding: 9px 10px;
            text-align: left;
            white-space: nowrap
        }

        tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700
        }

        .badge-received {
            background: #d1fae5;
            color: #065f46
        }

        .badge-paid {
            background: #d1fae5;
            color: #065f46
        }

        .badge-partial {
            background: #fef3c7;
            color: #92400e
        }

        .badge-due {
            background: #fee2e2;
            color: #991b1b
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 10px
        }

        .pay-table thead th {
            background: #28a745
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 14px
        }

        @media print {
            body {
                background: #fff;
                padding: 0
            }

            .toolbar {
                display: none
            }

            .card {
                box-shadow: none
            }
        }
    </style>
</head>

<body>

    <div class="toolbar">
        <div class="toolbar-title">
            Purchase Details <span style="color:#1a56db">(Reference No: #{{ $purchase->reference_no }})</span>
        </div>
        <div style="display:flex;gap:8px">
            <button onclick="window.print()" class="btn btn-print">
                <i class="bi bi-printer"></i> Print
            </button>
            <button onclick="window.close()" class="btn btn-close">
                <i class="bi bi-x-lg"></i> Close
            </button>
        </div>
    </div>

    {{-- Header info --}}
    <div class="card">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:16px">
            <div>
                <div class="meta-label">Supplier:</div>
                <div style="font-weight:600;font-size:14px">{{ $purchase->supplier_name }}</div>
                @if($purchase->supplier_address)
                <div style="font-size:12px;color:#64748b">{{ $purchase->supplier_address }}</div>
                @endif
            </div>
            <div>
                <div class="meta-label">Business:</div>
                <div style="font-weight:600;font-size:14px">S.B.S Shipping &amp; Trading Agencies (Pvt.) Ltd</div>
                <div style="font-size:12px;color:#64748b">{{ $purchase->business_location }}</div>
                <div style="font-size:12px;color:#64748b">Lalkhan Bazar, Chattogram</div>
            </div>
            <div style="text-align:right">
                <div class="meta-label">Reference No:</div>
                <div style="font-weight:700;color:#1a56db">#{{ $purchase->reference_no }}</div>
                <div class="meta-label" style="margin-top:6px">Date:</div>
                <div style="font-weight:600">{{ $purchase->purchase_date->format('d/m/Y') }}</div>
                <div style="margin-top:8px">
                    <span style="font-size:12px;color:#64748b">Purchase Status: </span>
                    <span class="badge badge-received">{{ $purchase->purchase_status }}</span>
                </div>
                <div style="margin-top:4px">
                    <span style="font-size:12px;color:#64748b">Payment Status: </span>
                    <span class="badge badge-{{ strtolower($purchase->payment_status) }}">{{ $purchase->payment_status }}</span>
                </div>
            </div>
        </div>

        {{-- Items table --}}
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Item Code</th>
                    <th>Purchase Quantity</th>
                    <th style="text-align:right">Unit Cost (Before Discount)</th>
                    <th style="text-align:center">Discount Percent</th>
                    <th style="text-align:right">Unit Cost (Before Tax)</th>
                    <th style="text-align:right">Subtotal (Before Tax)</th>
                    <th style="text-align:right">Tax</th>
                    <th style="text-align:right">Unit Cost Price (After Tax)</th>
                    <th style="text-align:right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:600">{{ $item->item_name }}</td>
                    <td style="color:#64748b">{{ $item->item_code ?? '—' }}</td>
                    <td style="text-align:center">{{ number_format($item->purchase_quantity, 2) }} {{ $item->unit ?? 'Nos' }}</td>
                    <td style="text-align:right">TK. {{ number_format($item->unit_cost, 2) }}</td>
                    <td style="text-align:center">{{ number_format($item->discount_percent, 2) }} %</td>
                    <td style="text-align:right">TK. {{ number_format($item->unit_cost_before_tax, 2) }}</td>
                    <td style="text-align:right">TK. {{ number_format($item->line_total, 2) }}</td>
                    <td style="text-align:right">TK. 0.00</td>
                    <td style="text-align:right">TK. {{ number_format($item->unit_cost_before_tax, 2) }}</td>
                    <td style="text-align:right">TK. {{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Payment info + Summary --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="card">
            <div class="section-title">Payment info:</div>
            <table class="pay-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Reference No</th>
                        <th style="text-align:right">Amount</th>
                        <th>Payment mode</th>
                        <th>Payment note</th>
                    </tr>
                </thead>
                <tbody>
                    @if($purchase->payment_amount > 0)
                    <tr>
                        <td>1</td>
                        <td>{{ $purchase->paid_on ? $purchase->paid_on->format('d/m/Y') : '—' }}</td>
                        <td style="color:#1a56db">PP{{ date('Y') }}/{{ str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td style="text-align:right;font-weight:600">TK. {{ number_format($purchase->payment_amount, 2) }}</td>
                        <td>{{ $purchase->payment_method }}</td>
                        <td style="color:#64748b">{{ $purchase->payment_note ?: '--' }}</td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="6" style="text-align:center;color:#94a3b8;padding:16px">No payments recorded</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="card">
            <div class="summary-row">
                <span style="color:#64748b">Net Total Amount:</span>
                <span>TK. {{ number_format($purchase->net_total, 2) }}</span>
            </div>
            <div class="summary-row">
                <span style="color:#64748b">Discount: <span style="color:#94a3b8">(-)</span></span>
                <span>TK. 0.00</span>
            </div>
            <div class="summary-row">
                <span style="color:#64748b">Purchase Tax: <span style="color:#94a3b8">(+)</span></span>
                <span>0.00</span>
            </div>
            <div class="summary-row">
                <span style="color:#64748b">Additional Shipping charges: <span style="color:#94a3b8">(+)</span></span>
                <span>0.00</span>
            </div>
            <div class="summary-row" style="margin-top:8px;padding-top:8px;border-top:2px solid #e2e8f0">
                <span>Purchase Total:</span>
                <span style="color:#1a56db">TK. {{ number_format($purchase->grand_total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Activity --}}
    <div class="card">
        <div class="section-title">Activities:</div>
        <table>
            <thead>
                <tr style="background:#f0f4ff">
                    <th style="color:#0f172a">Date</th>
                    <th style="color:#0f172a">Action</th>
                    <th style="color:#0f172a">By</th>
                    <th style="color:#0f172a">Note</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                    <td>Added</td>
                    <td>{{ $purchase->added_by ?? '—' }}</td>
                    <td>
                        Status: <span class="badge badge-received">{{ $purchase->purchase_status }}</span>
                        Total: <span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700">TK. {{ number_format($purchase->grand_total, 2) }}</span>
                        Payment Status: <span class="badge badge-{{ strtolower($purchase->payment_status) }}">{{ $purchase->payment_status }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>