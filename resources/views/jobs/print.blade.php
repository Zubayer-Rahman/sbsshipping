<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Details - {{ $job->job_id }}</title>
    
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
            font-size: 13px;
        }

        /* Print Controls (hidden when printing) */
        .print-controls {
            max-width: 800px;
            margin: 20px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-print, .btn-back {
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
        }

        .btn-print { background: #1a56db; color: #fff; }
        .btn-print:hover { background: #1340b0; }
        .btn-back { background: #fff; color: #64748b; border: 1px solid #e2e8f0; }

        /* Main Page Wrapper */
        .page-wrap {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 30px 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,.08);
        }

        /* ─── HEADER ─── */
        .company-header {
            text-align: center;
            padding-bottom: 12px;
            border-bottom: 1px solid #ccc;
            margin-bottom: 15px;
        }

        .company-header h1 {
            font-size: 26px;
            color: #1e3a8a;
            font-weight: 800;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .company-header h2 {
            font-size: 22px;
            color: #1e3a8a;
            font-weight: 700;
            margin-bottom: 8px;
        }

        /* Office Info Bar */
        .office-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 11px;
        }

        .office-block {
            flex: 1;
        }

        .office-block.left { text-align: left; }
        .office-block.right { text-align: right; }

        .office-block strong {
            font-size: 12px;
            color: #1e3a8a;
            font-weight: 700;
        }

        .office-block p {
            font-size: 11px;
            color: #333;
            margin-top: 2px;
            line-height: 1.5;
        }

        .agent-badge {
            border: 2px solid #1a56db;
            padding: 6px 18px;
            font-weight: 800;
            font-size: 13px;
            color: #1a56db;
            border-radius: 3px;
            background: #fff;
        }

        /* Job Details Title */
        .job-details-title {
            text-align: center;
            margin: 25px 0 20px;
        }

        .job-details-title h3 {
            display: inline-block;
            font-size: 16px;
            font-weight: 800;
            color: #000;
            padding-bottom: 4px;
            border-bottom: 2px solid #000;
            letter-spacing: 1px;
        }

        /* Top Info Section */
        .top-info {
            margin-bottom: 16px;
            font-size: 13px;
        }

        .top-info p {
            margin-bottom: 4px;
            font-weight: 600;
        }

        .top-info strong {
            font-weight: 700;
        }

        /* Section Headers */
        .section-header {
            font-size: 12px;
            font-weight: 800;
            color: #000;
            margin: 16px 0 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Info Table (4-column layout) */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .info-table td {
            border: 1px solid #1e3a8a;
            padding: 8px 12px;
            font-size: 12px;
            vertical-align: middle;
            background: #fff;
        }

        .info-table .label {
            font-weight: 700;
            background: #fff;
            width: 18%;
            color: #000;
        }

        .info-table .value {
            background: #fff;
            width: 32%;
            color: #000;
        }

        /* Expense Table */
        .expense-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .expense-table th {
            border: 1px solid #1e3a8a;
            padding: 8px 12px;
            background: #fff;
            font-weight: 700;
            font-size: 12px;
            text-align: center;
            color: #000;
        }

        .expense-table td {
            border: 1px solid #1e3a8a;
            padding: 8px 12px;
            font-size: 12px;
            text-align: center;
            color: #000;
        }

        /* Totals Bar */
        .totals-bar {
            text-align: center;
            padding: 12px 0;
            font-size: 13px;
            font-weight: 700;
            color: #000;
        }

        .totals-bar span {
            margin: 0 8px;
        }

        /* Footer Signatures */
        .signature-row {
            margin-top: 80px;
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .signature-cell {
            display: table-cell;
            border: 1px solid #1e3a8a;
            padding: 14px 16px;
            font-weight: 700;
            font-size: 13px;
            color: #000;
            width: 50%;
        }

        .signature-cell.right {
            text-align: right;
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
                padding: 15px 25px;
                max-width: 100%;
            }

            .print-controls {
                display: none !important;
            }

            @page {
                size: A4;
                margin: 10mm 15mm;
            }
        }
    </style>
</head>
<body>

    {{-- Print Controls (hidden when printing) --}}
    <div class="print-controls">
        <a href="{{ route('jobs.show', $job->id) }}" class="btn-back">← Back to Job</a>
        <button class="btn-print" onclick="window.print()">🖨 Print PDF</button>
    </div>

    <div class="page-wrap">

        {{-- ── COMPANY HEADER ── --}}
        <div class="company-header">
            <h1>S.B.S Shipping and Trading Agencies (Pvt.) Ltd.</h1>
            <h2>এস.বি.এস শিপিং এন্ড ট্রেডিং এজেন্সীস (প্রাঃ) লিমিটেড</h2>
        </div>

        {{-- ── OFFICE BAR ── --}}
        <div class="office-bar">
            <div class="office-block left">
                <strong>Head Office</strong>
                <p>345/E Chanmari Raod, Lalkhan Bazar, Chattogram</p>
                <p><strong>Phone :</strong> 02333358128, 02333514390,</p>
                <p><strong>E-mail:</strong> sbsshipping12@gmail.com</p>
            </div>
            <div class="agent-badge">
                C & F AGENT
            </div>
            <div class="office-block right">
                <strong>Dhaka Office</strong>
                <p>House # 71 (Gd.Fl.), Road # 27, Gulshan -1, Dhaka</p>
                <p><strong>Phone :</strong> 02333358128, 02333514390</p>
                <p><strong>E-mail:</strong> sbsshipping12@gmail.com</p>
            </div>
        </div>

        {{-- ── JOB DETAILS TITLE ── --}}
        <div class="job-details-title">
            <h3>JOB DETAILS</h3>
        </div>

        {{-- ── TOP INFO ── --}}
        <div class="top-info">
            <p><strong>Client Name :</strong> {{ $job->client_name ?? '—' }}</p>
            <p><strong>Job Number :</strong> {{ $job->job_no ?? $job->job_id ?? '—' }}</p>
            <p><strong>Job Receive Date :</strong> {{ optional($job->receive_date)->format('Y-m-d') ?? '—' }}</p>
        </div>

        {{-- ── OTHER INFO ── --}}
        <div class="section-header">OTHER INFO</div>

        <table class="info-table">
            <tr>
                <td class="label">Start Date</td>
                <td class="value">{{ optional($job->start_date)->format('Y-m-d') ?? '' }}</td>
                <td class="label">Status</td>
                <td class="value">{{ $job->status ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Invoice No.</td>
                <td class="value">{{ $job->invoice_no ?? '-' }}</td>
                <td class="label">BOE Number</td>
                <td class="value">{{ $job->be_no ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">BOE Date</td>
                <td class="value">{{ optional($job->be_date)->format('Y-m-d') ?? '' }}</td>
                <td class="label">IP/EP No</td>
                <td class="value">{{ $job->ip_ep_no ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">IP/EP Date</td>
                <td class="value">{{ optional($job->ip_ep_date)->format('Y-m-d') ?? '' }}</td>
                <td class="label">Items</td>
                <td class="value">{{ $job->items ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Quantity</td>
                <td class="value">{{ $job->quantity ?? '' }} {{ $job->type ?? '' }}</td>
                <td class="label">Bill No.</td>
                <td class="value">{{ $job->bill_no ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">To</td>
                <td class="value">{{ $job->destination ?? '' }}</td>
                <td class="label">From</td>
                <td class="value">{{ $job->origin ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Container No</td>
                <td class="value">{{ $job->container_no ?? '' }}</td>
                <td class="label">Buyer Name</td>
                <td class="value">{{ $job->buyer_name ?? '' }}</td>
            </tr>
            <tr>
                <td class="label">Shipping Agent</td>
                <td class="value">{{ $job->shipping_agent ?? '' }}</td>
                <td class="label">Status</td>
                <td class="value">{{ $job->status ?? '' }}</td>
            </tr>
        </table>

        {{-- ── JOB EXPENSES ── --}}
        <div class="section-header">JOB EXPENSES</div>

        <table class="expense-table">
            <thead>
                <tr>
                    <th style="width: 5%;">SL</th>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 18%;">Category</th>
                    <th style="width: 18%;">Sub Category</th>
                    <th style="width: 17%;">Note</th>
                    <th style="width: 15%;">Expense By</th>
                    <th style="width: 15%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Fetch expenses linked to this job
                    $jobExpenses = \App\Models\Expense::where('job_id', $job->id)->get();
                    $totalExpenses = $jobExpenses->sum('total_amount');
                    $totalInvoiced = $job->cost_amount ?? 0;
                    $profitLoss = $totalInvoiced - $totalExpenses;
                @endphp
                
                @forelse($jobExpenses as $idx => $expense)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ optional($expense->expense_date)->format('Y-m-d') ?? '—' }}</td>
                    <td>{{ $expense->expense_category ?? '—' }}</td>
                    <td>{{ $expense->sub_category ?? '—' }}</td>
                    <td>{{ Str::limit($expense->expense_note, 30) ?? '—' }}</td>
                    <td>{{ $expense->added_by ?? '—' }}</td>
                    <td>{{ number_format($expense->total_amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 20px; color: #888;">No expenses recorded</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ── TOTALS BAR ── --}}
        <div class="totals-bar">
            <span><strong>Total Invoiced Value:</strong> {{ number_format($totalInvoiced, 2) }}</span>
            <span>|</span>
            <span><strong>Total Expenses:</strong> {{ number_format($totalExpenses, 2) }}</span>
            <span>|</span>
            <span><strong>Profit / Loss:</strong> {{ number_format($profitLoss, 2) }}</span>
        </div>

        {{-- ── SIGNATURE FOOTER ── --}}
        <div class="signature-row">
            <div class="signature-cell">Audit By</div>
            <div class="signature-cell right">Approved By</div>
        </div>

    </div>

    <script>
        // Auto-trigger print if URL has ?auto=1
        if (window.location.search.includes('auto=1')) {
            setTimeout(() => window.print(), 500);
        }
    </script>

</body>
</html>