@extends('layouts.app')

@section('title', 'Create Job')
@section('page-title', 'Create Job')
@section('breadcrumb', 'Jobs Manager / Create Job')

@section('content')

<div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <h2 style="font-family:'Inter',sans-serif;font-size:32px;font-weight:800;color:var(--text-primary)">
            Create Job
        </h2>
        <a href="{{ route('jobs.list') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Jobs List
        </a>
    </div>

    <div class="card">
        <div class="card-header" style="padding:18px 24px 14px;border-bottom:1px solid var(--border)">
            <span style="font-size:15px;font-weight:700;color:var(--text-primary)">Create Job</span>
        </div>

        <form method="POST" action="{{ route('jobs.store') }}">
            @csrf
            <div style="padding:24px">

                {{-- ROW 1: JOB NO | AWB No | Start Date | Receive Date --}}
                <div class="cj-grid4" style="margin-bottom:20px">
                    <div class="form-group">
                        <label class="form-label">JOB NO</label>
                        <input type="text" name="job_no" class="form-control" value="{{ old('job_no') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">AWB No</label>
                        <input type="text" name="awb_no" class="form-control" value="{{ old('awb_no') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Receive Date</label>
                        <input type="date" name="receive_date" class="form-control" value="{{ old('receive_date') }}">
                    </div>
                </div>

                {{-- ROW 2: Client Name | User | Assign To | Category + Items + Qty --}}
                <div class="cj-grid4" style="margin-bottom:20px">
                    <div class="form-group">
                        <label class="form-label">Client Name</label>
                        <select name="client_name" class="form-select">
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->business_name }}"
                                {{ old('client_name') == $client->business_name ? 'selected' : '' }}>
                                {{ $client->business_name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-group">
                            <label class="form-label">User</label>
                            <select name="assigned_user" class="form-select">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_user') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                                <!-- <option value="{{ auth()->id() }}" selected>{{ auth()->user()->name }}</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Assign To</label>
                        <select name="assigned_agent" class="form-select cj-listbox" size="4">
                            <option value="SALMAN G AHMED" {{ old('assigned_agent')=='SALMAN G AHMED' ?'selected':'' }}>SALMAN G AHMED</option>
                            <option value="Pallab Hore" {{ old('assigned_agent')=='Pallab Hore'    ?'selected':'' }}>Pallab Hore</option>
                            <option value="Md. AL-Amin" {{ old('assigned_agent')=='Md. AL-Amin'    ?'selected':'' }}>Md. AL-Amin</option>
                        </select>
                    </div>

                    {{-- Job Group Selection --}}
                    <div class="form-group">
                        <label class="form-label">Job Group <span style="color:var(--text-muted);font-size:12px;font-weight:400">(Optional)</span></label>

                        <div style="display:flex;gap:8px;align-items:flex-start">
                            <select name="job_group_id" id="jobGroupSelect"
                                style="flex:1;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
                                <option value="">— No Group —</option>
                                @foreach(\App\Models\JobGroup::where('status', 'active')->orderBy('name')->get() as $group)
                                <option value="{{ $group->id }}">
                                    {{ $group->group_code }} • {{ $group->name }} ({{ $group->jobs()->count() }} jobs)
                                </option>
                                @endforeach
                            </select>

                            <button type="button" onclick="openNewGroupModal()"
                                style="padding:10px 16px;background:var(--success);color:#fff;border:none;
                           border-radius:6px;font-weight:600;cursor:pointer;font-size:13px;white-space:nowrap">
                                + New Group
                            </button>
                        </div>
                        <p style="font-size:12px;color:var(--text-muted);margin-top:4px">
                            Add this job to a group to organize related shipments
                        </p>

                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" style="margin-bottom:10px">
                            <option value="">Select Type</option>
                            <option value="FCL" {{ old('type')== 'FCL'  ?'selected':'' }}>FCL</option>
                            <option value="LCL" {{ old('type')== 'LCL'  ?'selected':'' }}>LCL</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select" style="margin-bottom:10px">
                            <option value="">Select Category</option>
                            <option value="Import by Air" {{ old('category')=='Import by Air'  ?'selected':'' }}>Import by Air</option>
                            <option value="Import by Sea" {{ old('category')=='Import by Sea'  ?'selected':'' }}>Import by Sea</option>
                            <option value="Export by Air" {{ old('category')=='Export by Air'  ?'selected':'' }}>Export by Air</option>
                            <option value="Export by Sea" {{ old('category')=='Export by Sea'  ?'selected':'' }}>Export by Sea</option>
                            <option value="By Truck (IMP)" {{ old('category')=='By Truck (IMP)'   ?'selected':'' }}>By Truck (IMP)</option>
                            <option value="By Truck (EXP)" {{ old('category')=='By Truck (EXP)'   ?'selected':'' }}>By Truck (EXP)</option>
                            <option value="Other" {{ old('category')=='Other' ?'selected':'' }}>Other</option>
                        </select>
                        <label class="form-label" style="margin-top:6px">Items</label>
                        <input type="text" name="items" class="form-control" value="{{ old('items') }}" style="margin-bottom:8px">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" min="0">
                    </div>
                </div>



                {{-- Quick New Group Modal --}}
                <div id="newGroupModal"
                    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;
            align-items:center;justify-content:center">
                    <div style="background:#fff;padding:24px;border-radius:12px;max-width:450px;width:90%">
                        <h3 style="margin:0 0 16px;font-size:18px;font-weight:700">Create New Job Group</h3>

                        <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">
                            Group Name <span style="color:var(--danger)">*</span>
                        </label>
                        <input type="text" id="newGroupName"
                            placeholder="e.g., Q1 2026 Imports"
                            style="width:100%;padding:10px 14px;border:1px solid var(--border);
                      border-radius:6px;font-size:14px;margin-bottom:16px">

                        <div id="newGroupError" style="display:none;color:var(--danger);font-size:13px;margin-bottom:12px"></div>

                        <div style="display:flex;gap:10px">
                            <button type="button" onclick="createNewGroup()"
                                style="flex:1;padding:10px;background:var(--primary);color:#fff;border:none;
                           border-radius:6px;font-weight:600;cursor:pointer">
                                Create Group
                            </button>
                            <button type="button" onclick="closeNewGroupModal()"
                                style="padding:10px 20px;background:#64748b;color:#fff;border:none;
                           border-radius:6px;font-weight:600;cursor:pointer">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <hr style="border:none;border-top:1px solid var(--border);margin:8px 0 20px">

                {{-- ROW 3: From | To | Cleared On | Vessel Name --}}
                <div class="cj-grid4" style="margin-bottom:20px">
                    <div class="form-group">
                        <label class="form-label">From</label>
                        <input type="text" name="origin" class="form-control" value="{{ old('origin') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">To</label>
                        <input type="text" name="destination" class="form-control" value="{{ old('destination') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cleared On</label>
                        <input type="date" name="cleared_on" class="form-control" value="{{ old('cleared_on') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vessel Name</label>
                        <input type="text" name="vessel_name" class="form-control" value="{{ old('vessel_name') }}">
                    </div>
                </div>

                {{-- ROW 4: Invoice No | Invoice Date | ROT No | Invoice Value (USD) --}}
                <div class="cj-grid4" style="margin-bottom:20px">
                    <div class="form-group">
                        <label class="form-label">Invoice No</label>
                        <input type="text" name="invoice_no" class="form-control" value="{{ old('invoice_no') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Invoice Date</label>
                        <input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ROT No</label>
                        <input type="text" name="rot_no" class="form-control" value="{{ old('rot_no') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Invoice Value (USD)</label>
                        <input type="number" name="invoice_value_usd" id="invoiceUsd" class="form-control" step="0.01" value="{{ old('invoice_value_usd') }}">
                    </div>
                </div>

                {{-- ROW 5: Exchange Rate | IMP./EXP. Value | B/E No | B/E Date --}}
                <div class="cj-grid4" style="margin-bottom:20px">
                    <div class="form-group">
                        <label class="form-label">Exchange Rate</label>
                        <input type="number" name="exchange_rate" id="exchangeRate" class="form-control" step="0.0001" value="{{ old('exchange_rate') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">IMP./EXP. Value</label>
                        <input type="number" name="imp_exp_value" id="impExpValue" class="form-control" step="0.01"
                            value="{{ old('imp_exp_value') }}" style="background:var(--body-bg)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">B/E No</label>
                        <input type="text" name="be_no" class="form-control"
                            value="{{ old('be_no', $job->be_no ?? '') }}"
                            placeholder="C-123456"
                            oninput="autoPrefixBE(this)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">B/E Date</label>
                        <input type="date" name="be_date" class="form-control" value="{{ old('be_date') }}">
                    </div>
                </div>

                {{-- ROW 6: IP/EP No | IP/EP Date | Container No | Shipping Agent --}}
                <div class="cj-grid4" style="margin-bottom:20px">
                    <div class="form-group">
                        <label class="form-label">IP/EP No</label>
                        <input type="number" name="ip_ep_no" class="form-control" value="{{ old('ip_ep_no') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">IP/EP Date</label>
                        <input type="date" name="ip_ep_date" class="form-control" value="{{ old('ip_ep_date') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Container No</label>
                        <input type="text" name="container_no" class="form-control" value="{{ old('container_no') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Shipping Agent</label>
                        <input type="text" name="shipping_agent" class="form-control" value="{{ old('shipping_agent') }}">
                    </div>
                </div>

                {{-- ROW 7: Buyer Name | Status --}}
                <div class="cj-grid4" style="margin-bottom:28px">
                    <div class="form-group">
                        <label class="form-label">Buyer Name</label>
                        <input type="text" name="buyer_name" class="form-control" value="{{ old('buyer_name') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Not Started" {{ old('status','Not Started')=='Not Started'?'selected':'' }}>Not Started</option>
                            <option value="pending" {{ old('status')=='pending'    ?'selected':'' }}>Pending</option>
                            <option value="in-transit" {{ old('status')=='in-transit' ?'selected':'' }}>In Transit</option>
                            <option value="delivered" {{ old('status')=='delivered'  ?'selected':'' }}>Delivered</option>
                            <option value="cancelled" {{ old('status')=='cancelled'  ?'selected':'' }}>Cancelled</option>
                        </select>
                    </div>
                    <div></div>
                    <div></div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-primary" style="min-width:120px;font-size:14px">
                    Save Job
                </button>

            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
    .cj-grid4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px 20px;
        align-items: start;
    }

    .cj-listbox {
        height: auto !important;
        min-height: 108px;
        padding: 4px 0 !important;
        overflow-y: auto;
    }

    .cj-listbox option {
        padding: 7px 12px;
        font-size: 13.5px;
        cursor: pointer;
    }

    .cj-listbox option:checked {
        background: var(--primary);
        color: #fff;
    }

    @media (max-width: 1100px) {
        .cj-grid4 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .cj-grid4 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-calculate IMP/EXP Value = Invoice USD × Exchange Rate
    const invoiceUsd = document.getElementById('invoiceUsd');
    const exchangeRate = document.getElementById('exchangeRate');
    const impExpValue = document.getElementById('impExpValue');

    function calcImpExp() {
        const usd = parseFloat(invoiceUsd.value) || 0;
        const rate = parseFloat(exchangeRate.value) || 0;
        if (usd && rate) impExpValue.value = (usd * rate).toFixed(2);
    }
    invoiceUsd.addEventListener('input', calcImpExp);
    exchangeRate.addEventListener('input', calcImpExp);


    // New job Group Modal Logic
    function openNewGroupModal() {
        document.getElementById('newGroupModal').style.display = 'flex';
        document.getElementById('newGroupName').focus();
        document.getElementById('newGroupError').style.display = 'none';
    }

    function closeNewGroupModal() {
        document.getElementById('newGroupModal').style.display = 'none';
        document.getElementById('newGroupName').value = '';
    }

    function createNewGroup() {
        const name = document.getElementById('newGroupName').value.trim();
        const errorDiv = document.getElementById('newGroupError');

        if (!name) {
            errorDiv.textContent = 'Please enter a group name';
            errorDiv.style.display = 'block';
            return;
        }

        fetch('/job-groups/quick-store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    name: name
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Add new option to dropdown and select it
                    const select = document.getElementById('jobGroupSelect');
                    const option = new Option(
                        `${data.group.group_code} • ${data.group.name} (0 jobs)`,
                        data.group.id,
                        true,
                        true
                    );
                    select.add(option);
                    closeNewGroupModal();
                } else {
                    errorDiv.textContent = data.message || 'Failed to create group';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(err => {
                errorDiv.textContent = 'Error: ' + err.message;
                errorDiv.style.display = 'block';
            });
    }

    function autoPrefixBE(input) {
        let val = input.value.trim();

        // If user types just numbers, add "C-" prefix on blur
        input.addEventListener('blur', function() {
            if (val && !val.startsWith('C-') && !val.startsWith('c-')) {
                input.value = 'C-' + val.replace(/^[Cc]-?/, '');
            }
        });
    }

    // Close modal on background click
    document.getElementById('newGroupModal').addEventListener('click', function(e) {
        if (e.target === this) closeNewGroupModal();
    });
</script>
@endpush