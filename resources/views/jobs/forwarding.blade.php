@extends('layouts.app')
@section('title','Create Forwarding Letter')
@section('page-title','Forwarding')
@section('breadcrumb','Jobs Manager / Forwarding')

@section('content')

<div>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px">
        <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">
            Create Forwarding Letter
        </h2>
        <a href="{{ route('forwarding.list') }}" class="btn btn-outline">
            <i class="bi bi-list-ul"></i> Forwarding List
        </a>
    </div>

    <form method="POST" action="{{ route('forwarding.store') }}" id="fwdForm">
        @csrf

        {{-- ── Card 1: Header fields ── --}}
        <div class="card" style="margin-bottom:16px">
            <div class="card-body" style="padding:24px">

                {{-- Select Customer --}}
                <div class="form-group" style="margin-bottom:18px">
                    <label class="form-label" style="font-weight:700">Select Customer:</label>
                    <select name="contact_id" id="contactSelect" class="form-select"
                        style="max-width:600px">
                        <option value="">-- Select Customer --</option>
                        @foreach($contacts as $c)
                        <option value="{{ $c->id }}"
                            data-name="{{ $c->business_name }}"
                            data-address="{{ $c->address }}">
                            {{ $c->business_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date + Ref No (hidden until customer chosen) --}}
                <div id="headerFields" style="display:none">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:600px;margin-bottom:18px">
                        <div class="form-group">
                            <label class="form-label" style="font-weight:600">Date:</label>
                            <input type="date" name="letter_date" class="form-control"
                                value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight:600">Ref No:</label>
                            <input type="text" name="ref_no" class="form-control" placeholder="Reference number">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label" style="font-weight:600">Subject</label>
                        <select name="subject" class="form-select">
                            <option value="">-- Select Subject --</option>
                            <option value="Sub: C&F Bill of Export Cargoes">Sub: C&amp;F Bill of Export Cargoes</option>
                            <option value="Sub: C&F Bill of Air Export Cargoes">Sub: C&amp;F Bill of Air Export Cargoes</option>
                            <option value="Sub: C&F Bill of Import Cargoes">Sub: C&amp;F Bill of Import Cargoes</option>
                            <option value="Sub: C&F Bill of Air Import Cargoes.(DK)">Sub: C&amp;F Bill of Air Import Cargoes.(DK)</option>
                            <option value="Sub: C&F Bill of Inter Zone">Sub: C&amp;F Bill of Inter Zone</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Card 2: Column toggles + Jobs table (shown after customer selected) ── --}}
        <div id="jobsSection" style="display:none">
            <div class="card" style="margin-bottom:16px">
                <div style="padding:14px 20px;border-bottom:1px solid var(--border);
                    display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                    <span style="font-size:13px;font-weight:700;color:var(--text-primary);margin-right:4px">
                        Show columns:
                    </span>
                    @php
                    $cols = [
                    'job_no' => 'Job Bill No',
                    'be_no' => 'Bill Number',
                    'ip_ep_no' => 'IP/EP No',
                    'ip_ep_date' => 'IP/EP Date',
                    'boe_no' => 'BOE No',
                    'awb_no' => 'AWB No',
                    'invoice_no' => 'Invoice No',
                    'invoice_value_usd' => 'Invoice Amount',
                    'buyer_name' => 'Buyer',
                    'vessel_name' => 'IIMS',
                    ];
                    @endphp
                    @foreach($cols as $key => $label)
                    <label style="display:inline-flex;align-items:center;gap:5px;
                          background:var(--body-bg);border:1px solid var(--border);
                          padding:4px 10px;border-radius:6px;cursor:pointer;
                          font-size:12px;font-weight:600;user-select:none;transition:all .15s"
                        onmouseover="this.style.borderColor='var(--primary)'"
                        onmouseout="this.style.borderColor='var(--border)'">
                        <input type="checkbox" name="visible_columns[]"
                            value="{{ $key }}" checked
                            style="accent-color:var(--primary);width:13px;height:13px"
                            onchange="toggleCol('{{ $key }}', this.checked)">
                        {{ $label }}
                    </label>
                    @endforeach
                </div>

                {{-- Loading spinner --}}
                <div id="jobsLoading" style="display:none;padding:40px;text-align:center;color:var(--text-muted)">
                    <i class="bi bi-arrow-repeat" style="font-size:28px;animation:spin 1s linear infinite"></i>
                    <div style="margin-top:8px;font-size:13px">Loading jobs...</div>
                </div>

                {{-- Jobs table --}}
                <div id="jobsTableWrap" style="overflow-x:auto;display:none">
                    <table id="jobsTable" style="width:100%;min-width:900px;border-collapse:collapse;font-size:13px">
                        <thead>
                            <tr style="background:#1a1a2e;color:#fff">
                                <th style="padding:10px 12px;width:36px">
                                    <input type="checkbox" id="checkAll"
                                        style="accent-color:#fff;width:14px;height:14px">
                                </th>
                                <th style="padding:10px 12px;text-align:left;white-space:nowrap">Job Bill No</th>
                                <th class="col-be_no" style="padding:10px 12px;text-align:left;white-space:nowrap">Bill Number</th>
                                <th class="col-ip_ep_no" style="padding:10px 12px;text-align:left;white-space:nowrap">IP/EP No</th>
                                <th class="col-ip_ep_date" style="padding:10px 12px;text-align:left;white-space:nowrap">IP/EP Date</th>
                                <th class="col-boe_no" style="padding:10px 12px;text-align:left;white-space:nowrap">BOE No</th>
                                <th class="col-awb_no" style="padding:10px 12px;text-align:left;white-space:nowrap">AWB No</th>
                                <th class="col-invoice_no" style="padding:10px 12px;text-align:left;white-space:nowrap">Invoice No</th>
                                <th class="col-invoice_value_usd" style="padding:10px 12px;text-align:right;white-space:nowrap">Invoice Amount</th>
                                <th class="col-buyer_name" style="padding:10px 12px;text-align:left;white-space:nowrap">Buyer</th>
                                <th class="col-vessel_name" style="padding:10px 12px;text-align:left;white-space:nowrap">IIMS</th>
                            </tr>
                        </thead>
                        <tbody id="jobsTbody" style="font-size:13px">
                            {{-- filled via JS --}}
                        </tbody>
                    </table>
                </div>

                <div id="noJobs" style="display:none;padding:40px;text-align:center;color:var(--text-muted)">
                    <i class="bi bi-inbox" style="font-size:36px;display:block;margin-bottom:8px;opacity:.35"></i>
                    No jobs found for this client.
                </div>
            </div>

            {{-- Bank Details --}}
            <div class="card" style="margin-bottom:20px">
                <div class="card-body" style="padding:20px">
                    <label class="form-label" style="font-weight:700;margin-bottom:8px;display:block">
                        Bank Details:
                    </label>
                    <textarea name="bank_details" class="form-control"
                        placeholder="Enter bank details..."
                        style="min-height:100px;resize:vertical"></textarea>
                </div>
            </div>

            {{-- Hidden field to carry selected job ids --}}
            <div id="hiddenJobIds"></div>

            {{-- Submit --}}
            <div style="display:flex;gap:12px;align-items:center">
                <button type="submit" class="btn"
                    style="background:#28a745;color:#fff;padding:12px 32px;
                       font-size:15px;font-weight:700;border-radius:8px;
                       box-shadow:0 4px 14px rgba(40,167,69,.3)">
                    <i class="bi bi-file-earmark-pdf"></i> Generate PDF
                </button>
                <span style="font-size:13px;color:var(--text-muted)">
                    Selected: <strong id="selectedCount">0</strong> job(s)
                </span>
            </div>
        </div>

    </form>
</div>

@endsection

@push('styles')
<style>
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    #jobsTable tbody tr {
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        transition: background .12s;
    }

    #jobsTable tbody tr:hover {
        background: #f0f4ff;
    }

    #jobsTable tbody tr.selected {
        background: #e8f0fe;
    }

    #jobsTable tbody td {
        padding: 9px 12px;
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
    const contactSelect = document.getElementById('contactSelect');
    const headerFields = document.getElementById('headerFields');
    const jobsSection = document.getElementById('jobsSection');
    const jobsTbody = document.getElementById('jobsTbody');
    const jobsLoading = document.getElementById('jobsLoading');
    const jobsTableWrap = document.getElementById('jobsTableWrap');
    const noJobs = document.getElementById('noJobs');
    const selectedCount = document.getElementById('selectedCount');
    const hiddenIds = document.getElementById('hiddenJobIds');

    let allJobs = [];

    contactSelect.addEventListener('change', function() {
        if (!this.value) {
            headerFields.style.display = 'none';
            jobsSection.style.display = 'none';
            return;
        }
        headerFields.style.display = 'block';
        jobsSection.style.display = 'block';
        loadJobs(this.value);
    });

    function loadJobs(contactId) {
        jobsLoading.style.display = 'flex';
        jobsTableWrap.style.display = 'none';
        noJobs.style.display = 'none';
        jobsLoading.style.display = 'block';
        jobsTbody.innerHTML = '';

        fetch(`/forwarding/jobs-for-contact?contact_id=${contactId}`)
            .then(r => r.json())
            .then(jobs => {
                jobsLoading.style.display = 'none';
                allJobs = jobs;
                if (!jobs.length) {
                    noJobs.style.display = 'block';
                    return;
                }
                jobsTableWrap.style.display = 'block';
                renderRows(jobs);
            })
            .catch(() => {
                jobsLoading.style.display = 'none';
                noJobs.style.display = 'block';
            });
    }

    function renderRows(jobs) {
        jobsTbody.innerHTML = '';
        jobs.forEach(j => {
            const tr = document.createElement('tr');
            tr.dataset.id = j.id;
            tr.innerHTML = `
            <td><input type="checkbox" class="job-check" data-id="${j.id}"
                       style="accent-color:var(--primary);width:14px;height:14px"></td>
            <td style="font-weight:700;color:var(--primary)">${j.job_no ?? j.job_id ?? '—'}</td>
            <td class="col-be_no">${j.be_no ?? '—'}</td>
            <td class="col-ip_ep_no">${j.ip_ep_no ?? '—'}</td>
            <td class="col-ip_ep_date">${j.ip_ep_date ? j.ip_ep_date.substring(0,10) : '—'}</td>
            <td class="col-boe_no">${j.be_no ?? '—'}</td>
            <td class="col-awb_no">${j.awb_no ?? '—'}</td>
            <td class="col-invoice_no">${j.invoice_no ?? '—'}</td>
            <td class="col-invoice_value_usd" style="text-align:right">${j.invoice_value_usd ? parseFloat(j.invoice_value_usd).toFixed(2) : '0.00'}</td>
            <td class="col-buyer_name">${j.buyer_name ?? '—'}</td>
            <td class="col-vessel_name">${j.vessel_name ?? '—'}</td>
        `;
            // Click row to toggle
            tr.addEventListener('click', function(e) {
                if (e.target.tagName === 'INPUT') return;
                const cb = tr.querySelector('.job-check');
                cb.checked = !cb.checked;
                updateRow(tr, cb);
            });
            tr.querySelector('.job-check').addEventListener('change', function() {
                updateRow(tr, this);
            });
            jobsTbody.appendChild(tr);
        });
        updateCount();
    }

    function updateRow(tr, cb) {
        tr.classList.toggle('selected', cb.checked);
        updateCount();
    }

    function updateCount() {
        const checked = document.querySelectorAll('.job-check:checked');
        selectedCount.textContent = checked.length;
        // Sync hidden inputs
        hiddenIds.innerHTML = '';
        checked.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'selected_jobs[]';
            inp.value = cb.dataset.id;
            hiddenIds.appendChild(inp);
        });
    }

    // Check all
    document.getElementById('checkAll').addEventListener('change', function() {
        document.querySelectorAll('.job-check').forEach(cb => {
            cb.checked = this.checked;
            updateRow(cb.closest('tr'), cb);
        });
    });

    // Column toggle
    function toggleCol(key, show) {
        document.querySelectorAll('.col-' + key).forEach(el => {
            el.style.display = show ? '' : 'none';
        });
    }

    // Prevent submit if none selected
    document.getElementById('fwdForm').addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.job-check:checked');
        if (!checked.length) {
            e.preventDefault();
            alert('Please select at least one job before generating the PDF.');
        }
    });
</script>
@endpush