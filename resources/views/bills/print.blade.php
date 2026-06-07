<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bill #{{ $bill->bill_no }} - {{ config('app.name') }}</title>

    <style>
        /* ─── PRINT-OPTIMIZED RESET ─── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: #1f2937;
            background: #f5f5f5;
            line-height: 1.5;
            font-size: 13px;
        }

        /* ─── PAGE LAYOUT ─── */
        .page-wrap {
            max-width: 850px;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .08);
            padding: 40px;
            border-radius: 4px;
        }

        /* ─── PRINT BUTTON (Hidden when printing) ─── */
        .print-controls {
            max-width: 850px;
            margin: 20px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-print,
        .btn-back {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all .2s;
        }

        .btn-print {
            background: #1a56db;
            color: #fff;
        }

        .btn-print:hover {
            background: #1340b0;
            transform: translateY(-1px);
        }

        .btn-back {
            background: #fff;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .btn-back:hover {
            background: #f1f5f9;
        }

        /* ─── HEADER ─── */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 25px;
            border-bottom: 3px solid #1a56db;
            margin-bottom: 30px;
        }

        .company-info h1 {
            font-size: 28px;
            color: #0f1f4b;
            margin-bottom: 8px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .company-info p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title .label {
            font-size: 32px;
            font-weight: 800;
            color: #1a56db;
            letter-spacing: -1px;
            line-height: 1;
            margin-bottom: 8px;
        }

        .invoice-title .meta {
            font-size: 12px;
            color: #64748b;
            line-height: 1.8;
        }

        .invoice-title .meta strong {
            color: #1f2937;
        }

        /* ─── INFO GRID ─── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .info-block {
            padding: 16px;
            background: #f9fafb;
            border-left: 3px solid #1a56db;
            border-radius: 4px;
        }

        .info-block .label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .info-block .name {
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .info-block .details {
            font-size: 12px;
            color: #4b5563;
            line-height: 1.6;
        }

        /* ─── ITEMS TABLE ─── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table thead {
            background: #0f1f4b;
            color: #fff;
        }

        .items-table th {
            padding: 12px 10px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .items-table th.text-right {
            text-align: right;
        }

        .items-table th.text-center {
            text-align: center;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .items-table td {
            padding: 12px 10px;
            font-size: 13px;
            color: #1f2937;
        }

        .items-table td.text-right {
            text-align: right;
        }

        .items-table td.text-center {
            text-align: center;
        }

        .item-name {
            font-weight: 600;
            color: #1f2937;
        }

        .item-desc {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        /* ─── ADDITIONAL EXPENSES ─── */
        .add-exp-section {
            margin: 25px 0;
            padding: 16px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
        }

        .add-exp-section h3 {
            font-size: 13px;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .add-exp-list {
            list-style: none;
        }

        .add-exp-list li {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px dashed #fde68a;
            font-size: 12px;
        }

        .add-exp-list li:last-child {
            border-bottom: none;
        }

        .add-exp-list .desc {
            color: #4b5563;
        }

        .add-exp-list .amt {
            font-weight: 700;
            color: #1f2937;
        }

        /* ─── TOTALS ─── */
        .totals-wrap {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }

        .totals-table {
            width: 350px;
            border-collapse: collapse;
        }

        .totals-table tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .totals-table td {
            padding: 10px 16px;
            font-size: 13px;
        }

        .totals-table td.label {
            color: #64748b;
            font-weight: 500;
        }

        .totals-table td.value {
            text-align: right;
            font-weight: 700;
            color: #1f2937;
        }

        .totals-table tr.grand-total {
            background: #0f1f4b;
            color: #fff;
            border: none;
        }

        .totals-table tr.grand-total td {
            padding: 14px 16px;
            font-size: 16px;
            font-weight: 800;
            color: #fff;
        }

        .totals-table tr.paid td {
            color: #10b981;
        }

        .totals-table tr.due td {
            color: #ef4444;
        }

        /* ─── PAYMENT STATUS BADGE ─── */
        .payment-status {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }

        .status-partial {
            background: #fef3c7;
            color: #92400e;
        }

        .status-due {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ─── NOTES ─── */
        .notes-section {
            margin: 30px 0;
            padding: 16px;
            background: #f9fafb;
            border-radius: 6px;
            border-left: 3px solid #1a56db;
        }

        .notes-section h4 {
            font-size: 12px;
            font-weight: 700;
            color: #1a56db;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        .notes-section p {
            font-size: 12px;
            color: #4b5563;
            line-height: 1.6;
        }

        /* ─── FOOTER ─── */
        .invoice-footer {
            margin-top: 50px;
            padding-top: 25px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
        }

        .signature-area {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 30px;
        }

        .signature-block {
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #1f2937;
            margin-bottom: 8px;
            padding-top: 6px;
            min-height: 50px;
        }

        .signature-label {
            font-size: 12px;
            color: #4b5563;
            font-weight: 600;
        }

        .footer-text {
            font-size: 11px;
            color: #9ca3af;
            line-height: 1.6;
        }

        .footer-text .thank-you {
            font-size: 16px;
            font-weight: 700;
            color: #1a56db;
            display: block;
            margin-bottom: 8px;
        }

        /* ─── PRINT STYLES ─── */
        @media print {
            body {
                background: #fff;
                margin: 0;
                padding: 0;
            }

            .page-wrap {
                margin: 0;
                box-shadow: none;
                padding: 20px;
                max-width: 100%;
                border-radius: 0;
            }

            .print-controls {
                display: none !important;
            }

            .invoice-header {
                page-break-after: avoid;
            }

            .items-table {
                page-break-inside: auto;
            }

            .items-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .totals-wrap,
            .signature-area {
                page-break-inside: avoid;
            }

            @page {
                size: A4;
                margin: 15mm;
            }
        }
    </style>
</head>

<body>

    {{-- PRINT BUTTONS (Hidden when printing) --}}
    <div class="print-controls">
        <a href="{{ route('bills.show', $bill->id) }}" class="btn-back">
            ← Back to Bill
        </a>
        <button class="btn-print" onclick="window.print()">
            🖨 Print Invoice
        </button>
    </div>

    <div class="page-wrap">

        {{-- ── HEADER ── --}}
        <div class="invoice-header">
            <div class="company-info">
                <h1>SBS SHIPPING</h1>
                <p>
                    {{ $bill->business_location ?? 'SBS Shipping (BL0001)' }}<br>
                    Dhaka, Bangladesh<br>
                    📞 +880 XXX-XXXXXX | ✉ info@sbsshipping.com
                </p>
            </div>
            <div class="invoice-title">
                <div class="label">INVOICE</div>
                <div class="meta">
                    <strong>Bill No:</strong> #{{ $bill->bill_no }}<br>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($bill->billing_date)->format('d M Y') }}<br>
                    <strong>Status:</strong>
                    <span class="payment-status status-{{ strtolower($bill->payment_status) }}">
                        {{ $bill->payment_status }}
                    </span>
                </div>
            </div>
        </div>

        {{-- ── CLIENT INFO ── --}}
        <div class="info-grid">
            <div class="info-block">
                <div class="label">Bill To</div>
                <div class="name">{{ $bill->client_name }}</div>
                <div class="details">
                    {{ $bill->billing_address }}<br>
                    @if($bill->client_contact)
                    📞 {{ $bill->client_contact }}
                    @endif
                </div>
            </div>
            <div class="info-block">
                <div class="label">Ship To</div>
                <div class="name">{{ $bill->client_name }}</div>
                <div class="details">
                    {{ $bill->shipping_address ?? $bill->billing_address }}<br>
                    @if($bill->job_number)
                    <strong>Job #:</strong> {{ $bill->job_number }}
                    @endif
                </div>
            </div>
        </div>

        {{-- ── ITEMS TABLE ── --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Item / Description</th>
                    <th class="text-center" style="width:80px">Qty</th>
                    <th class="text-right" style="width:120px">Unit Price</th>
                    <th class="text-right" style="width:130px">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bill->items as $idx => $item)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->item_name }}</div>
                        @if($item->description)
                        <div class="item-desc">{{ $item->description }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">৳ {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">৳ {{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:20px;color:#9ca3af">
                        No items
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ── ADDITIONAL EXPENSES ── --}}
        @if($bill->additionalExpenses && $bill->additionalExpenses->count() > 0)
        <div class="add-exp-section">
            <h3>Additional Expenses</h3>
            <ul class="add-exp-list">
                @foreach($bill->additionalExpenses as $expense)
                <li>
                    <span class="desc">
                        @if($expense->is_auto) ⚡ @endif
                        {{ $expense->description }}
                    </span>
                    <span class="amt">৳ {{ number_format($expense->amount, 2) }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- ── TOTALS ── --}}
        <div class="totals-wrap">
            <table class="totals-table">
                <tr>
                    <td class="label">Sub Total ({{ number_format($bill->total_items, 0) }} items)</td>
                    <td class="value">৳ {{ number_format($bill->sub_total, 2) }}</td>
                </tr>

                @if($bill->additionalExpenses && $bill->additionalExpenses->count() > 0)
                <tr>
                    <td class="label">Additional Expenses</td>
                    <td class="value">৳ {{ number_format($bill->additionalExpenses->sum('amount'), 2) }}</td>
                </tr>
                @endif

                @if($bill->shipping_charges > 0)
                <tr>
                    <td class="label">Shipping Charges</td>
                    <td class="value">৳ {{ number_format($bill->shipping_charges, 2) }}</td>
                </tr>
                @endif

                @if($bill->discount_value > 0)
                <tr>
                    <td class="label">Discount</td>
                    <td class="value">- ৳ {{ number_format($bill->discount_value, 2) }}</td>
                </tr>
                @endif

                @if($bill->order_tax_value > 0)
                <tr>
                    <td class="label">Tax</td>
                    <td class="value">+ ৳ {{ number_format($bill->order_tax_value, 2) }}</td>
                </tr>
                @endif

                <tr class="grand-total">
                    <td>TOTAL PAYABLE</td>
                    <td class="value">৳ {{ number_format($bill->total_payable, 2) }}</td>
                </tr>

                @if($bill->total_paid > 0)
                <tr class="paid">
                    <td class="label">Amount Paid</td>
                    <td class="value">৳ {{ number_format($bill->total_paid, 2) }}</td>
                </tr>
                @endif

                @if($bill->total_remaining > 0)
                <tr class="due">
                    <td class="label">Balance Due</td>
                    <td class="value">৳ {{ number_format($bill->total_remaining, 2) }}</td>
                </tr>
                @endif
            </table>
        </div>

        {{-- ── PAYMENT INFO ── --}}
        @if($bill->total_paid > 0)
        <div class="info-block" style="margin-bottom:25px;background:#f0fdf4;border-left-color:#10b981">
            <div class="label" style="color:#15803d">Payment Information</div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-top:8px;font-size:12px">
                <div>
                    <strong>Method:</strong><br>
                    <span style="color:#4b5563">{{ $bill->payment_method ?? '—' }}</span>
                </div>
                <div>
                    <strong>Paid On:</strong><br>
                    <span style="color:#4b5563">{{ \Carbon\Carbon::parse($bill->paid_on)->format('d M Y') }}</span>
                </div>
                <div>
                    <strong>Account:</strong><br>
                    <span style="color:#4b5563">{{ $bill->payment_account ?? '—' }}</span>
                </div>
            </div>
        </div>
        @endif

        {{-- ── NOTES ── --}}
        @if($bill->billing_note)
        <div class="notes-section">
            <h4>📝 Notes</h4>
            <p>{{ $bill->billing_note }}</p>
        </div>
        @endif

        {{-- ── FOOTER ── --}}
        <div class="invoice-footer">
            <div class="signature-area">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-label">Customer Signature</div>
                </div>
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-label">Authorized Signature</div>
                </div>
            </div>

            <div class="footer-text">
                <span class="thank-you">Thank You for Your Business!</span>
                This is a computer-generated invoice. No signature is required for verification.<br>
                For any queries, please contact us at info@sbsshipping.com<br>
                <strong>{{ $bill->added_by ?? 'SBS Shipping Team' }}</strong>
            </div>
        </div>

    </div>

    <script>
        // Auto-trigger print dialog if URL has ?auto=1
        if (window.location.search.includes('auto=1')) {
            setTimeout(() => window.print(), 500);
        }
    </script>

</body>

</html>