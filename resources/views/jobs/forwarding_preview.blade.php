<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Forwarding Letter — {{ $letter->ref_no ?? $letter->id }}</title>
<link href="https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Times New Roman', Times, serif;
    font-size: 13px;
    color: #000;
    background: #e8e8e8;
}

/* ── Toolbar ── */
.toolbar {
    position: fixed; top: 0; left: 0; right: 0;
    background: #1a56db; color: #fff;
    padding: 10px 24px;
    display: flex; align-items: center; justify-content: space-between;
    z-index: 100; box-shadow: 0 2px 8px rgba(0,0,0,.2);
    gap: 12px; flex-wrap: wrap;
}
.toolbar-title { font-size: 15px; font-weight: 700; font-family: sans-serif; }
.toolbar-btns  { display: flex; gap: 10px; }
.tbtn {
    padding: 7px 18px; border-radius: 6px; border: none;
    font-size: 13px; font-weight: 600; cursor: pointer;
    font-family: sans-serif; display: inline-flex; align-items: center; gap: 6px;
    text-decoration: none;
}
.tbtn-print  { background: #10b981; color: #fff; }
.tbtn-back   { background: rgba(255,255,255,.15); color: #fff; border: 1px solid rgba(255,255,255,.3); }
.tbtn-save   { background: #f59e0b; color: #fff; }

/* ── Paper ── */
.paper-wrap {
    margin-top: 60px;
    padding: 30px 20px 60px;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.paper {
    width: 210mm;
    min-height: 297mm;
    background: #fff;
    padding: 14mm 16mm;
    box-shadow: 0 4px 24px rgba(0,0,0,.15);
    position: relative;
}

/* ── Letter Head ── */
.lh-wrapper {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 8px;
    align-items: start;
    border-bottom: 2px solid #000;
    padding-bottom: 8px;
    margin-bottom: 10px;
}
.lh-left, .lh-right { font-size: 11px; line-height: 1.6; }
.lh-center { text-align: center; }
.lh-name-bn { font-size: 18px; font-weight: 900; color: #003087; line-height: 1.2; }
.lh-name-local { font-size: 13px; font-weight: 700; color: #003087; }
.lh-badge {
    border: 2px solid #000; padding: 4px 10px;
    font-size: 12px; font-weight: 700; margin-top: 6px;
    display: inline-block;
}

/* ── Meta ── */
.letter-meta { margin: 10px 0 8px; font-size: 12px; line-height: 1.9; }
.to-block    { margin-bottom: 8px; font-size: 12px; line-height: 1.7; }
.subject-line{ font-size: 12px; font-weight: 700; text-align: center; text-decoration: underline; margin-bottom: 8px; }
.dear-line   { font-size: 12px; margin-bottom: 6px; }
.body-text   { font-size: 12px; margin-bottom: 10px; line-height: 1.6; }

/* ── Table ── */
.fwd-table {
    width: 100%; border-collapse: collapse; font-size: 11.5px; margin-bottom: 8px;
}
.fwd-table th {
    border: 1px solid #000; padding: 5px 6px;
    background: #f0f0f0; font-weight: 700;
    text-align: center; white-space: nowrap;
}
.fwd-table td {
    border: 1px solid #000; padding: 5px 6px; text-align: center;
}
.fwd-table .total-row td {
    font-weight: 700; background: #f9f9f9;
}
.fwd-table td:nth-child(2) { text-align: left; }

/* ── Footer ── */
.taka-words { font-size: 12px; font-weight: 700; margin-bottom: 4px; }
.encl-line  { font-size: 12px; margin-bottom: 8px; }
.bank-block { font-size: 12px; margin-bottom: 14px; }
.sign-block { font-size: 12px; text-align: right; margin-top: 30px; }

/* ── Print ── */
@media print {
    body { background: #fff; }
    .toolbar { display: none; }
    .paper-wrap { margin-top: 0; padding: 0; }
    .paper {
        width: 100%; min-height: auto;
        box-shadow: none; padding: 10mm 14mm;
    }
    @page { size: A4; margin: 0; }
}
</style>
</head>
<body>

{{-- ── Toolbar ── --}}
<div class="toolbar">
    <span class="toolbar-title">
        <i>📄</i> Forwarding Letter Preview
        @if($letter->ref_no) — Ref: {{ $letter->ref_no }} @endif
    </span>
    <div class="toolbar-btns">
        <a href="{{ route('jobs.forwarding') }}" class="tbtn tbtn-back">← Back</a>
        <button onclick="window.print()" class="tbtn tbtn-print">🖨 Print</button>
        <button onclick="window.print()" class="tbtn tbtn-save">💾 Save as PDF</button>
    </div>
</div>

<div class="paper-wrap">
<div class="paper">

    {{-- ── Letter Head ── --}}
    <div class="lh-wrapper">
        <div class="lh-left">
            <strong>Head Office</strong><br>
            345/E Chanmari Road, Lalkhan Bazar, Chattogram<br>
            Phone : 02333358128, 02333514390,<br>
            E-mail: sbshipping12@gmail.com
        </div>
        <div class="lh-center">
            <div class="lh-name-bn">S.B.S Shipping and Trading Agencies (Pvt.) Ltd.</div>
            <div class="lh-name-local">এস.বি,এস শিপিং এন্ড ট্রেডিং এজেন্সীস (প্রাঃ) লিমিটেড</div>
            <div><span class="lh-badge">C &amp; F AGENT</span></div>
        </div>
        <div class="lh-right" style="text-align:right">
            <strong>Dhaka Office</strong><br>
            House # 71 (Gd.Fl.), Road # 27, Gulshan -1, Dhaka<br>
            Phone : 02333358128, 02333514390<br>
            E-mail: sbshipping12@gmail.com
        </div>
    </div>

    {{-- ── Meta ── --}}
    <div class="letter-meta">
        <strong>Date:</strong> {{ $letter->letter_date ? $letter->letter_date->format('Y-m-d') : '' }}<br>
        <strong>Ref No:</strong> {{ $letter->ref_no }}
    </div>

    <div class="to-block">
        <strong>To</strong><br>
        @if($contact)
            <strong>{{ strtoupper($contact->business_name) }}</strong><br>
            {{ $contact->address }}
        @endif
    </div>

    @if($letter->subject)
    <div class="subject-line">{{ $letter->subject }}</div>
    @endif

    <div class="dear-line"><strong>Dear Sir,</strong></div>
    <div class="body-text">
        We are pleased to submit herewith the following C&amp;F bills along with connected
        documents for your early payment please.
    </div>

    {{-- ── Jobs Table ── --}}
    @php
        $visibleCols = $letter->visible_columns ?? array_keys(['job_no'=>1,'be_no'=>1,'ip_ep_no'=>1,'ip_ep_date'=>1,'boe_no'=>1,'awb_no'=>1,'invoice_no'=>1,'invoice_value_usd'=>1,'buyer_name'=>1,'vessel_name'=>1]);
        $colMap = [
            'job_no'            => 'Job No',
            'be_no'             => 'Bill Number',
            'ip_ep_no'          => 'IP/EP No',
            'ip_ep_date'        => 'IP/EP Date',
            'boe_no'            => 'BOE No.',
            'awb_no'            => 'AWB No',
            'invoice_no'        => 'Invoice No',
            'invoice_value_usd' => 'Amount (TK.)',
            'buyer_name'        => 'Buyer',
            'vessel_name'       => 'IIMS',
        ];
        $total = 0;
    @endphp

    <table class="fwd-table">
        <thead>
            <tr>
                <th>Sl.</th>
                @foreach($colMap as $key => $label)
                    @if(in_array($key, $visibleCols))
                        <th>{{ $label }}</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($jobs as $i => $job)
            @php
                $amt = floatval($job->invoice_value_usd ?? 0);
                $total += $amt;
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                @foreach($colMap as $key => $label)
                    @if(in_array($key, $visibleCols))
                    <td>
                        @if($key === 'invoice_value_usd')
                            {{ number_format($amt, 2) }}
                        @elseif($key === 'ip_ep_date')
                            {{ $job->ip_ep_date ? substr($job->ip_ep_date, 0, 10) : '-' }}
                        @else
                            {{ $job->$key ?? '-' }}
                        @endif
                    </td>
                    @endif
                @endforeach
            </tr>
            @endforeach
            {{-- Total row --}}
            <tr class="total-row">
                <td colspan="{{ count(array_intersect(array_keys($colMap), $visibleCols)) }}"
                    style="text-align:right;font-weight:700">Total Amount:</td>
                <td style="font-weight:700">{{ number_format($total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ── Words ── --}}
    @php
        function numberToWords($num) {
            $num = (int) round($num);
            $ones = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine',
                     'Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen',
                     'Seventeen','Eighteen','Nineteen'];
            $tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
            if ($num === 0) return 'Zero';
            $words = '';
            if ($num >= 10000000) { $words .= numberToWords(intval($num/10000000)).' Crore '; $num %= 10000000; }
            if ($num >= 100000)   { $words .= numberToWords(intval($num/100000)).' Lakh ';   $num %= 100000; }
            if ($num >= 1000)     { $words .= numberToWords(intval($num/1000)).' Thousand '; $num %= 1000; }
            if ($num >= 100)      { $words .= $ones[intval($num/100)].' Hundred ';           $num %= 100; }
            if ($num >= 20)       { $words .= $tens[intval($num/10)].' '; $num %= 10; }
            if ($num > 0)         { $words .= $ones[$num].' '; }
            return trim($words);
        }
    @endphp
    <div class="taka-words">
        Taka (In words) : {{ numberToWords($total) }} Taka Only
    </div>
    <div class="encl-line"><strong>Encl:</strong> (1) Bill/Documents</div>

    @if($letter->bank_details)
    <div class="bank-block">
        <strong>Bank Details:</strong><br>
        {{ $letter->bank_details }}
    </div>
    @endif

    <div style="font-size:12px;margin-bottom:4px">Thanking you.</div>
    <div style="font-size:12px;margin-bottom:30px">Yours faithfully,</div>

    <div class="sign-block">
        For <strong>S.B.S. Shipping &amp; Trading Agencies (Pvt.) Ltd</strong>
    </div>

</div>{{-- /paper --}}
</div>{{-- /paper-wrap --}}

</body>
</html>