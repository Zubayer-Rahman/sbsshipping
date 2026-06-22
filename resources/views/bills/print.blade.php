@php
function numberToWordsHelper($num) {
$ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
$tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

if ($num == 0) return 'Zero';
if ($num < 20) return $ones[$num];
    if ($num < 100) return $tens[intval($num/10)] . ($num%10 ? ' ' . $ones[$num%10] : '' );
    if ($num < 1000) return $ones[intval($num/100)] . ' Hundred' . ($num%100 ? ' ' . numberToWordsHelper($num%100) : '' );
    if ($num < 100000) return numberToWordsHelper(intval($num/1000)) . ' Thousand' . ($num%1000 ? ' ' . numberToWordsHelper($num%1000) : '' );
    if ($num < 10000000) return numberToWordsHelper(intval($num/100000)) . ' Lakh' . ($num%100000 ? ' ' . numberToWordsHelper($num%100000) : '' );
    return numberToWordsHelper(intval($num/10000000)) . ' Crore' . ($num%10000000 ? ' ' . numberToWordsHelper($num%10000000) : '' );
    }

    function numberToWords($number) {
    $number=floatval($number);
    $whole=floor($number);
    $decimal=round(($number - $whole) * 100);

    $words=numberToWordsHelper($whole);
    if ($decimal> 0) {
    $words .= ' Point ' . numberToWordsHelper($decimal);
    }
    return $words;
    }
    @endphp
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Bill #{{ $bill->bill_no }} - {{ config('app.name') }}</title>

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                font-family: 'Arial', 'Segoe UI', sans-serif;
                color: #000;
                background: #f5f5f5;
                line-height: 1.4;
                font-size: 12px;
            }

            .print-controls {
                max-width: 800px;
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
            }

            .btn-print {
                background: #1a56db;
                color: #fff;
            }

            .btn-back {
                background: #fff;
                color: #64748b;
                border: 1px solid #e2e8f0;
            }

            .page-wrap {
                max-width: 800px;
                margin: 20px auto;
                background: #fff;
                padding: 25px 35px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, .08);
            }

            .company-header {
                text-align: center;
            }

            .company-header h1 {
                font-size: 24px;
                color: #215092;
                font-weight: 800;
                margin-bottom: 4px;
            }

            .company-header h2 {
                font-size: 24px;
                color: black;
                font-weight: 700;
                margin-bottom: 6px;
            }

            .office-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 6px 0;
                font-size: 11px;
            }

            .office-block {
                flex: 1;
            }

            .office-block.left {
                text-align: left;
            }

            .office-block.right {
                text-align: right;
            }

            .office-block strong {
                font-size: 12px;
                color: #1e3a8a;
                font-weight: 700;
            }

            .office-block p {
                font-size: 11px;
                font-weight: 700;
                color: #333;
                margin-top: 2px;
                line-height: 1.5;
            }

            .agent-badge {
                border: 2px solid #1a56db;
                padding: 6px 18px;
                font-weight: 800;
                font-size: 13px;
                color: black;
                border-radius: 3px;
                background: #fff;
            }

            .bill-info-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            .bill-info-table td {
                border: 1px solid black;
                padding: 5px 8px;
                font-size: 12px;
                font-weight: 700;
                color: #000;
            }

            .client-info-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: -1px;
            }

            .client-info-table td {
                border: 1px solid black;
                padding: 4px;
                font-size: 12px;
                vertical-align: top;
            }

            .client-info-table td.label {
                font-weight: 700;
                width: 18%;
            }

            .client-info-table td.value {
                font-weight: 600;
                width: 32%;
            }

            .client-name-row td {
                font-weight: 800;
                font-size: 13px;
                background: #fff;
            }

            .particulars-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 30px;
            }

            .particulars-table th {
                border: 1px solid black;
                padding: 6px 10px;
                font-size: 12px;
                font-weight: 800;
                text-align: left;
                background: #fff;
                color: #000;
                letter-spacing: 2px;
            }

            .particulars-table th.amount-col {
                text-align: right;
                width: 25%;
            }

            .particulars-table td {
                border: 1px solid black;
                padding: 4px 5px;
                font-size: 12px;
                color: #000;
            }

            .particulars-table td.amount {
                text-align: right;
                font-weight: 600;
            }

            .particulars-table tr.subheader td {
                text-align: right;
                font-size: 11px;
                font-weight: 700;
                padding: 3px 10px;
            }

            .particulars-table tr.total-row td {
                font-weight: 800;
                font-size: 13px;
            }

            .particulars-table tr.total-row td.label-cell {
                text-align: right;
            }

            .particulars-table tr.grand-total td {
                font-weight: 800;
                text-align: center;
                font-size: 12px;
                padding: 6px 10px;
            }

            .enclosure-section {
                margin-top: 30px;
            }

            .enclosure-section h4 {
                font-size: 13px;
                font-weight: 800;
                text-decoration: underline;
                margin-bottom: 5px;
            }

            .enclosure-table {
                width: 100%;
                border-collapse: collapse;
            }

            .enclosure-table td {
                border: 1px solid black;
                padding: 2px;
                font-size: 11px;
                font-weight: 600;
                width: 25%;
            }

            .footer-signature {
                text-align: right;
                margin-top: 70px;
                font-weight: 800;
                font-size: 13px;
                color: #215092;
            }

            @media print {
                body {
                    background: #fff;
                    margin: 0;
                    padding: 0;
                }

                .page-wrap {
                    margin: 0;
                    box-shadow: none;
                    padding: 10px 20px;
                    max-width: 100%;
                }

                .print-controls {
                    display: none !important;
                }

                @page {
                    size: A4;
                    margin: 10mm 12mm;
                }
            }
        </style>
    </head>

    <body>

        <div class="print-controls">
            <a href="{{ route('bills.show', $bill->id) }}" class="btn-back">← Back to Bill</a>
            <button class="btn-print" onclick="window.print()">🖨 Print PDF</button>
        </div>

        <div class="page-wrap">

            {{-- COMPANY HEADER --}}
            <div class="company-header">
                <h1>S.B.S Shipping and Trading Agencies (Pvt.) Ltd.</h1>
                <h2>এস.বি.এস শিপিং এন্ড ট্রেডিং এজেন্সীস (প্রাঃ) লিমিটেড</h2>
            </div>

            {{-- OFFICE BAR --}}
            <div class="office-bar">
                <div class="office-block left">
                    <strong>Head Office</strong>
                    <p>345/E Chanmari Raod, Lalkhan Bazar, Chattogram</p>
                    <p><strong>Phone :</strong> 02333358128, 02333514390,</p>
                    <p><strong>E-mail:</strong> sbsshipping12@gmail.com</p>
                </div>
                <div class="agent-badge">C & F AGENT</div>
                <div class="office-block right">
                    <strong>Dhaka Office</strong>
                    <p>House # 71 (Gd.Fl.), Road # 27, Gulshan -1, Dhaka</p>
                    <p><strong>Phone :</strong> 02333358128, 02333514390</p>
                    <p><strong>E-mail:</strong> sbsshipping12@gmail.com</p>
                </div>
            </div>

            {{-- BILL HEADER INFO --}}
            <table class="bill-info-table">
                <tr>
                    <td style="width:40%">REC. DT- {{ \Carbon\Carbon::parse($bill->billing_date)->format('Y-m-d') }}</td>
                    <td style="width:30%">Job No: {{ $bill->job_number ?? '—' }}</td>
                    <td style="width:30%">Date: {{ \Carbon\Carbon::parse($bill->billing_date)->format('d-m-Y') }}</td>
                </tr>
            </table>

            {{-- CLIENT INFO TABLE --}}
            <table class="client-info-table">
                <tr class="client-name-row">
                    <td colspan="4">{{ strtoupper($bill->client_name) }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="font-weight:600">{{ $bill->billing_address ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Items:</td>
                    <td class="value" colspan="3">{{ $job->items ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">SS:</td>
                    <td class="value">{{ $job->category ?? '—' }}</td>
                    <td class="label">From:</td>
                    <td class="value">{{ $job->origin ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Quantity:</td>
                    <td class="value">{{ $job->quantity ?? '—' }} {{ $job->type ?? '' }}</td>
                    <td class="label">To:</td>
                    <td class="value">{{ $job->destination ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">AWB No:</td>
                    <td class="value">{{ $job->awb_no ?? '—' }}</td>
                    <td class="label">Cleared On:</td>
                    <td class="value">{{ $job->cleared_on ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Inv. No.:</td>
                    <td class="value">{{ $job->invoice_no ?? '—' }}</td>
                    <td class="label">DT:</td>
                    <td class="value">{{ !empty($job->invoice_date) ? \Carbon\Carbon::parse($job->invoice_date)->format('Y-m-d') : '—' }}</td>
                </tr>
                <tr>
                    <td class="label">ROT No:</td>
                    <td class="value">{{ $job->rot_no ?? '—' }}</td>
                    <td class="label">DT :</td>
                    <td class="value">{{ $job->rot_date ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">USD</td>
                    <td class="value">{{ $job->invoice_value_usd ?? '—' }}</td>
                    <td class="label">Exch. Rate</td>
                    <td class="value">{{ $job->exchange_rate ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">B/E No.:</td>
                    <td class="value">{{ $job->be_no ?? '—' }}</td>
                    <td class="label">DT:</td>
                    <td class="value">{{ !empty($job->be_date) ? \Carbon\Carbon::parse($job->be_date)->format('Y-m-d') : '—' }}</td>
                </tr>
                <tr>
                    <td class="label">IP/EP No:</td>
                    <td class="value">{{ $job->ip_ep_no ?? '—' }}</td>
                    <td class="label">IMP./EXP. Value :</td>
                    <td class="value">{{ !empty($job->imp_exp_value) ? number_format($job->imp_exp_value, 4) : '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Container No:</td>
                    <td class="value">{{ $job->container_no ?? '—' }}</td>
                    <td class="label">DT:</td>
                    <td class="value">{{ !empty($job->ip_ep_date) ? \Carbon\Carbon::parse($job->ip_ep_date)->format('Y-m-d') : '—' }}</td>
                </tr>
            </table>

            {{-- PARTICULARS TABLE --}}
            <table class="particulars-table">
                <thead>
                    <tr>
                        <th>P A R T I C U L A R S</th>
                        <th class="amount-col">A M O U N T</th>
                    </tr>
                    <tr class="subheader">
                        <td></td>
                        <td class="amount">Taka / Ps.</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bill->items as $item)
                    <tr>
                        <td>
                            {{ $item->item_name }}
                            @if($item->description) - {{ $item->description }} @endif
                        </td>
                        <td class="amount">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="text-align:center;color:#888">No items</td>
                    </tr>
                    @endforelse

                    @if($bill->additionalExpenses && $bill->additionalExpenses->count() > 0)
                    @foreach($bill->additionalExpenses as $expense)
                    <tr>
                        <td>{{ $expense->description }}</td>
                        <td class="amount">{{ number_format($expense->amount, 2) }}</td>
                    </tr>
                    @endforeach
                    @endif

                    <tr class="total-row">
                        <td class="label-cell">Total</td>
                        <td class="amount">TK. {{ number_format($bill->total_payable, 2) }}</td>
                    </tr>

                    <tr class="grand-total">
                        <td colspan="2">
                            Total : TK. {{ number_format($bill->total_payable, 2) }}
                            &nbsp;|&nbsp;
                            Inwords : {{ ucwords(numberToWords($bill->total_payable)) }} Only
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- ENCLOSURE --}}
            <div class="enclosure-section">
                <h4>Enclosure:</h4>
                <table class="enclosure-table">
                    <tr>
                        <td>Invoice -</td>
                        <td>License Permit -</td>
                        <td>Undertaking -</td>
                        <td>Biman Fee -</td>
                    </tr>
                    <tr>
                        <td>B/E Custom Trip -</td>
                        <td>C & F Subscription -</td>
                        <td>Quota -</td>
                        <td>VAT (Original) -</td>
                    </tr>
                    <tr>
                        <td>D/Challan</td>
                        <td>R/D & W/C, L/C -</td>
                        <td>Bank G'Fee -</td>
                        <td>Agent Charges -</td>
                    </tr>
                </table>
            </div>

            {{-- FOOTER SIGNATURE --}}
            <div class="footer-signature">
                For - S.B.S Shipping & Trading Agencies (Pvt.) Ltd.
            </div>

        </div>

        <script>
            if (window.location.search.includes('auto=1')) {
                setTimeout(() => window.print(), 500);
            }
        </script>

    </body>

    </html>