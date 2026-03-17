@extends('layouts.app')

@section('title', 'Forwarding')
@section('page-title', 'Forwarding')
@section('breadcrumb', 'Jobs Manager / Forwarding')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div>
        <h2 style="font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            Forwarding & Bill Generation
        </h2>
        <p style="font-size:13px;color:var(--text-muted);margin-top:3px">
            Generate bills for specific clients
        </p>
    </div>
</div>

<div class="card" style="text-align:center;padding:60px 40px">
    <div style="width:72px;height:72px;background:var(--primary-light);border-radius:20px;
                display:flex;align-items:center;justify-content:center;
                font-size:36px;color:var(--primary);margin:0 auto 20px">
        <i class="bi bi-file-earmark-text"></i>
    </div>
    <h3 style="font-family:'Inter',sans-serif;font-size:20px;font-weight:800;
               color:var(--text-primary);margin-bottom:10px">
        Forwarding / Bill Generation
    </h3>
    <p style="color:var(--text-muted);font-size:14px;max-width:440px;margin:0 auto 24px;line-height:1.7">
        This section will allow you to select a client and generate a detailed bill (invoice)
        for their jobs. You mentioned you'll provide more details on this — it's ready to be built!
    </p>
    <div style="background:var(--body-bg);border-radius:var(--radius);padding:20px;
                max-width:500px;margin:0 auto;text-align:left">
        <p style="font-size:13px;font-weight:700;color:var(--text-primary);margin-bottom:8px">
            📋 Planned features for this page:
        </p>
        <ul style="font-size:13px;color:var(--text-muted);list-style:none;display:flex;flex-direction:column;gap:6px">
            <li>✦ Select a client from dropdown</li>
            <li>✦ View all jobs for that client</li>
            <li>✦ Generate a printable / PDF invoice</li>
            <li>✦ Mark jobs as billed / forwarded</li>
            <li>✦ Email invoice to client</li>
        </ul>
    </div>
    <p style="margin-top:24px;font-size:13px;color:var(--text-muted)">
        Provide more details and this will be fully built out in the next step!
    </p>
</div>

@endsection