@extends('layouts.app')
@section('title','Add Bill')
@section('page-title','Add Bill')
@section('breadcrumb','Bill / Add Bill')

@section('content')
<div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
        <h2 style="font-family:'Inter',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">Add Bill</h2>
        <a href="{{ route('bills.list') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> All Bills</a>
    </div>

    <form method="POST" action="{{ route('bills.store') }}" id="billForm">
        @csrf

        <!-- {{-- ── SECTION 1: Business Location ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:18px 24px">
                <div style="display:flex;align-items:center;gap:8px;max-width:420px">
                    <span style="padding:0 10px;border:1.5px solid var(--border);border-right:none;
                             border-radius:var(--radius-sm) 0 0 var(--radius-sm);height:40px;
                             display:flex;align-items:center;background:var(--body-bg)">
                        <i class="bi bi-geo-alt" style="color:var(--text-muted)"></i>
                    </span>
                    <select class="form-select" style="border-radius:0 var(--radius-sm) var(--radius-sm) 0;flex:1">
                        <option value="BL0001">SBS Shipping (BL0001)</option>
                    </select>
                    <span class="info-dot" title="Business Location">i</span>
                </div>
            </div>
        </div> -->

        {{-- ── SECTION 2: Client + Dates ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">
                <div class="bill-grid4">

                    {{-- Client --}}
                    <div class="form-group">
                        <label class="form-label">Client:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;gap:8px;align-items:center">
                            <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;flex:1">
                                <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                    <i class="bi bi-person" style="color:var(--text-muted)"></i>
                                </span>
                                <select name="client_id" id="clientSelect" class="form-select" style="border:none;border-radius:0;flex:1" required onchange="loadClientInfo(this.value)">
                                    <option value="">(Select Client)</option>
                                    @foreach($clients as $c)
                                    <option value="{{ $c->id }}"
                                        data-name="{{ $c->business_name }}"
                                        data-address="{{ $c->address }}"
                                        data-mobile="{{ $c->mobile }}"
                                        data-balance="{{ $c->advance_balance ?? 0 }}">
                                        ({{ $c->contact_id ?? 'C'.$c->id }}) {{ $c->business_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="clientBalance" style="display:none;font-size:12px;color:var(--danger);margin-top:4px;font-weight:600"></div>
                    </div>

                    {{-- Billing Date --}}
                    <div class="form-group">
                        <label class="form-label">Billing Date:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-calendar3" style="color:var(--text-muted)"></i>
                            </span>
                            <input type="datetime-local" name="billing_date" class="form-control"
                                style="border:none;border-radius:0;flex:1"
                                value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label class="form-label">Status:<span style="color:var(--danger)">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="">Please Select</option>
                            <option value="Final" selected>Final</option>
                            <option value="Draft">Draft</option>
                        </select>
                    </div>

                </div>

                {{-- Row 2: Addresses + Job Number --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px 20px;margin-top:16px" id="clientDetails" style="display:none">
                    <div class="form-group">
                        <label class="form-label">Billing Address:</label>
                        <div id="billingAddress" style="font-size:13px;color:var(--text-primary);padding:8px 12px;background:var(--body-bg);border-radius:var(--radius-sm);min-height:60px;line-height:1.6"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Shipping Address:</label>
                        <div id="shippingAddress" style="font-size:13px;color:var(--text-primary);padding:8px 12px;background:var(--body-bg);border-radius:var(--radius-sm);min-height:60px;line-height:1.6"></div>
                    </div>
                    {{-- Job Selection Dropdown --}}
                    <div class="form-group">
                        <label class="form-label">Select Job(s) <span style="color:var(--text-muted);font-size:12px;font-weight:400">(Service charge will be auto-added)</span></label>

                        <div id="jobTrigger"
                            style="display:flex;align-items:center;gap:8px;
               border:1.5px solid var(--border);border-radius:var(--radius-sm);
               background:#fff;padding:0 12px;height:42px;cursor:pointer;
               transition:border-color .2s"
                            onclick="toggleJobDropdown(event)">
                            <i style="color:var(--text-muted);font-size:14px">🔍</i>
                            <input type="text" id="jobSearch"
                                placeholder="Search by Job ID or Client Name..."
                                autocomplete="off"
                                style="border:none;outline:none;flex:1;font-size:14px;
                   font-family:'Inter',sans-serif;background:transparent;
                   cursor:text;color:var(--text-primary)"
                                onclick="event.stopPropagation();openJobDropdown()"
                                oninput="filterJobs(this.value)">
                            <span id="jobChevron" style="color:var(--text-muted);font-size:12px;transition:transform .2s">▼</span>
                        </div>

                        <div id="jobTags" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px"></div>

                        <input type="hidden" name="job_number" id="jobNumberHidden">

                        <p style="font-size:12px;color:var(--text-muted);margin-top:5px;margin-bottom:0">
                            Click on jobs to select. Selected jobs will appear as tags below.
                        </p>
                    </div>

                    {{-- Hidden checkbox pool to track selections --}}
                    <div id="jobCheckboxPool" style="display:none">
                        @foreach(\App\Models\Job::orderBy('id', 'desc')->get() as $job)
                        <input type="checkbox"
                            class="job-check"
                            value="{{ $job->id }}"
                            data-ref="{{ $job->job_id ?? $job->job_no }}"
                            data-client="{{ $job->client_name }}"
                            data-category="{{ $job->category }}"
                            data-invoice="{{ $job->invoice_value_usd }}"
                            data-imp-exp="{{ $job->imp_exp_value }}">
                        @endforeach
                    </div>

                    {{-- Floating Dropdown Panel --}}
                    <div id="jobFloatingDropdown"
                        style="display:none;position:fixed;background:#fff;
           border:1.5px solid var(--primary);border-radius:var(--radius-sm);
           box-shadow:0 8px 28px rgba(15,31,75,.14);
           max-height:380px;z-index:9999;flex-direction:column;overflow:hidden">

                        <div style="display:flex;justify-content:space-between;align-items:center;
                padding:10px 14px;border-bottom:1px solid var(--border);
                background:var(--body-bg);flex-shrink:0">
                            <span style="font-size:12px;font-weight:700;color:var(--text-primary)">
                                Select Jobs (Multiple Allowed)
                            </span>
                            <button type="button" onclick="clearAllJobs()"
                                style="font-size:12px;color:var(--danger);background:none;
                   border:none;cursor:pointer;font-weight:600;padding:0">
                                Clear All
                            </button>
                        </div>

                        <div id="jobVisualList" style="overflow-y:auto;flex:1;padding:4px">
                            @foreach(\App\Models\Job::orderBy('id', 'desc')->get() as $job)
                            <div class="job-visual-option"
                                data-id="{{ $job->id }}"
                                data-search="{{ strtolower(($job->job_id ?? $job->job_no) . ' ' . $job->client_name) }}"
                                style="display:flex;align-items:center;gap:12px;padding:12px 14px;
                        cursor:pointer;border-bottom:1px solid var(--border);
                        transition:background .15s">

                                {{-- Checkbox visual --}}
                                <span class="job-visual-check"
                                    style="width:18px;height:18px;border:2px solid var(--border);
                             border-radius:4px;flex-shrink:0;display:flex;
                             align-items:center;justify-content:center;
                             transition:all .2s;background:#fff">
                                </span>

                                {{-- Job info --}}
                                <div style="flex:1;min-width:0">
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px">
                                        <span style="font-size:13px;font-weight:700;color:var(--primary);
                                     font-family:'Inter',sans-serif">
                                            {{ $job->job_id ?? $job->job_no ?? 'Job #' . $job->id }}
                                        </span>
                                        @if($job->category)
                                        <span style="font-size:10px;padding:2px 6px;border-radius:10px;
                                         background:{{ str_contains(strtolower($job->category), 'import') ? '#dbeafe' : '#fef3c7' }};
                                         color:{{ str_contains(strtolower($job->category), 'import') ? '#1e40af' : '#92400e' }};
                                         font-weight:600;text-transform:uppercase">
                                            {{ $job->category }}
                                        </span>
                                        @endif

                                        @if($job->type)
                                        <span style="font-size:10px;padding:2px 6px;border-radius:10px;
                                         background:{{ str_contains(strtolower($job->type), 'import') ? '#dbeafe' : '#fef3c7' }};
                                         color:{{ str_contains(strtolower($job->type    ), 'import') ? '#1e40af' : '#92400e' }};
                                         font-weight:600;text-transform:uppercase">
                                            {{ $job->type }}
                                        </span>
                                        @endif
                                    </div>
                                    <div style="font-size:12px;color:var(--text-muted);
                                overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                        👤 {{ $job->client_name ?? 'No client' }}
                                    </div>
                                </div>

                                {{-- Invoice Value --}}
                                @if($job->invoice_value_usd)
                                <div style="text-align:right;flex-shrink:0">
                                    <div style="font-size:11px;color:var(--text-muted)">Invoice</div>
                                    <div style="font-size:12px;font-weight:700;color:var(--text-primary)">
                                        ${{ number_format($job->invoice_value_usd, 0) }}
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>

                        <div id="jobNoResults"
                            style="display:none;padding:20px;text-align:center;font-size:13px;
               color:var(--text-muted)">
                            No jobs found matching your search.
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── SECTION 3: Items ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:20px 24px">

                {{-- Item table --}}
                <div style="overflow-x:auto;margin-bottom:14px">
                    <table style="width:100%;min-width:800px;border-collapse:collapse;font-size:13px">
                        <thead>
                            <tr style="background:var(--body-bg)">
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:center;width:36px;font-size:11px;text-transform:uppercase;color:var(--text-muted)">#</th>
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:left;font-size:11px;text-transform:uppercase;color:var(--text-muted)">Item</th>
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:left;font-size:11px;text-transform:uppercase;color:var(--text-muted)">Description</th>
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:center;font-size:11px;text-transform:uppercase;color:var(--text-muted);width:100px">Quantity</th>
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:right;font-size:11px;text-transform:uppercase;color:var(--text-muted);width:120px">Amount</th>
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:right;font-size:11px;text-transform:uppercase;color:var(--text-muted);width:120px">Subtotal</th>
                                <th style="padding:10px 12px;border-bottom:2px solid var(--border);text-align:center;width:40px">
                                    <span style="font-size:18px;font-weight:700;color:var(--text-muted)">✕</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="billItemsBody"></tbody>
                    </table>
                </div>

                {{-- Totals row --}}
                <div style="display:flex;justify-content:flex-end;padding:8px 12px;font-size:13px;gap:24px">
                    <span style="color:var(--text-muted)">Items: <strong id="totalItemsDisp">0.00</strong></span>
                    <span style="color:var(--text-muted)">Total: <strong id="totalDisp" style="color:var(--primary)">0.00</strong></span>
                </div>

                {{-- Item search --}}
                <div style="display:flex;align-items:center;gap:10px;margin-top:4px">
                    <div style="position:relative;flex:1;max-width:500px">
                        <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px"></i>
                        <input type="text" id="billItemSearch" placeholder="Enter Items name"
                            class="form-control" style="padding-left:36px"
                            oninput="searchBillItems(this.value)" autocomplete="off">
                        <div id="billItemSuggestions"
                            style="display:none;position:absolute;top:calc(100%+4px);left:0;right:0;
                                background:#fff;border:1.5px solid var(--primary);border-radius:var(--radius-sm);
                                box-shadow:0 8px 24px rgba(15,31,75,.12);z-index:500;max-height:200px;overflow-y:auto">
                        </div>
                    </div>
                    <button type="button" onclick="addBillRow()"
                        style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;
                               border-radius:6px;background:transparent;color:var(--primary);
                               border:1.5px solid var(--primary);font-size:13px;font-weight:600;cursor:pointer">
                        <i class="bi bi-plus-lg"></i> Add new item
                    </button>
                </div>

            </div>
        </div>

        {{-- ── SECTION 4: Additional Expenses ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:24px">

                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);margin:0">
                        Additional Expenses
                    </h3>
                    <button type="button" onclick="addExpenseRow()"
                        style="background:var(--primary);color:#fff;border:none;padding:8px 16px;
                           border-radius:var(--radius-sm);font-weight:600;cursor:pointer;
                           font-family:'Inter',sans-serif;font-size:13px;
                           display:flex;align-items:center;gap:6px">
                        <span style="font-size:18px;line-height:1">+</span> Add Expense
                    </button>
                </div>

                {{-- Expense Table --}}
                <div style="border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                    <table id="additionalExpensesTable" style="width:100%;border-collapse:collapse;font-family:'Inter',sans-serif">
                        <thead>
                            <tr style="background:var(--body-bg)">
                                <th style="padding:10px 14px;text-align:left;font-size:12px;font-weight:700;
                                   text-transform:uppercase;color:var(--text-muted);letter-spacing:0.05em;
                                   border-bottom:1px solid var(--border);width:50px">#</th>
                                <th style="padding:10px 14px;text-align:left;font-size:12px;font-weight:700;
                                   text-transform:uppercase;color:var(--text-muted);letter-spacing:0.05em;
                                   border-bottom:1px solid var(--border)">Description</th>
                                <th style="padding:10px 14px;text-align:right;font-size:12px;font-weight:700;
                                   text-transform:uppercase;color:var(--text-muted);letter-spacing:0.05em;
                                   border-bottom:1px solid var(--border);width:200px">Amount (৳)</th>
                                <th style="padding:10px 14px;text-align:center;font-size:12px;font-weight:700;
                                   text-transform:uppercase;color:var(--text-muted);letter-spacing:0.05em;
                                   border-bottom:1px solid var(--border);width:80px">Action</th>
                            </tr>
                        </thead>
                        <tbody id="expenseRowsBody">
                            {{-- Rows will be added here by JavaScript --}}
                        </tbody>
                        <tfoot>
                            <tr style="background:var(--body-bg);border-top:2px solid var(--border)">
                                <td colspan="2" style="padding:12px 14px;text-align:right;font-weight:700;
                                              font-size:14px;color:var(--text-primary)">
                                    Total Additional Expenses:
                                </td>
                                <td style="padding:12px 14px;text-align:right;font-weight:700;font-size:16px;
                                   color:var(--primary)">
                                    ৳<span id="totalAdditionalExpenses">0.00</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Empty State (shown when no rows) --}}
                <div id="noExpensesMessage" style="text-align:center;padding:2rem;color:var(--text-muted);font-size:14px">
                    No additional expenses added. Click "Add Expense" to start.
                </div>

                {{-- Hidden input to store total for form submission --}}
                <input type="hidden" name="additional_expenses_total" id="additionalExpensesTotal" value="0">

                {{-- Billing Note --}}
                <div class="form-group" style="margin-top:20px">
                    <label class="form-label">Billing Note</label>
                    <textarea name="billing_note" class="form-control"
                        style="min-height:80px;resize:vertical"
                        placeholder="Any additional notes for this bill..."></textarea>
                </div>

            </div>
        </div>

        {{-- ── SECTION 5: Total Payable ── --}}
        <div class="card" style="margin-bottom:6px">
            <div class="card-body" style="padding:16px 24px;display:flex;align-items:center;justify-content:flex-end;gap:16px">
                <div style="font-size:15px;font-weight:700;color:var(--text-primary)">
                    Total Payable: TK. <span id="totalPayableDisp">0.00</span>
                </div>
            </div>
        </div>

        {{-- ── SECTION 6: Add Payment ── --}}
        <div class="card" style="margin-bottom:24px">
            <div style="padding:16px 24px 14px;border-bottom:1px solid var(--border)">
                <span style="font-size:15px;font-weight:700;color:var(--text-primary)">Add payment</span>
            </div>
            <div class="card-body" style="padding:24px">

                <div style="font-size:13px;color:var(--text-muted);margin-bottom:16px">
                    Advance Balance: TK. <strong id="advanceBalance">0.00</strong>
                </div>

                <div class="bill-grid3" style="margin-bottom:18px">
                    <div class="form-group">
                        <label class="form-label">Amount:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-cash" style="color:var(--text-muted)"></i>
                            </span>
                            <input type="number" name="payment_amount" class="form-control"
                                style="border:none;border-radius:0;flex:1" value="0.00" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Paid on:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-calendar3" style="color:var(--text-muted)"></i>
                            </span>
                            <input type="datetime-local" name="paid_on" class="form-control"
                                style="border:none;border-radius:0;flex:1"
                                value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Payment Method:<span style="color:var(--danger)">*</span></label>
                        <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                            <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                                <i class="bi bi-credit-card" style="color:var(--text-muted)"></i>
                            </span>
                            <select name="payment_method" class="form-select" style="border:none;border-radius:0;flex:1">
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="bKash">bKash</option>
                                <option value="Nagad">Nagad</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="max-width:380px;margin-bottom:18px">
                    <label class="form-label">Payment Account:</label>
                    <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                        <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                            <i class="bi bi-cash-stack" style="color:var(--text-muted)"></i>
                        </span>
                        <select name="payment_account" class="form-select" style="border:none;border-radius:0;flex:1">
                            <option value="None">None</option>
                            <option value="Cash in Hand">Cash in Hand</option>
                            <option value="Bank Account">Bank Account</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Payment note:</label>
                    <textarea name="payment_note" class="form-control" style="min-height:80px;resize:vertical" placeholder="Payment notes..."></textarea>
                </div>

            </div>
        </div>

        {{-- Buttons --}}
        <div style="display:flex;justify-content:center;gap:14px;margin-bottom:32px">
            <button type="submit" name="action" value="save" class="btn"
                style="background:#7c3aed;color:#fff;min-width:120px;font-size:15px;font-weight:700;
                       padding:13px 32px;border-radius:8px;box-shadow:0 4px 14px rgba(124,58,237,.3)">
                Save
            </button>
            <button type="submit" name="action" value="save_print" class="btn"
                style="background:#10b981;color:#fff;min-width:160px;font-size:15px;font-weight:700;
                       padding:13px 32px;border-radius:8px;box-shadow:0 4px 14px rgba(16,185,129,.3)">
                Save and print
            </button>
        </div>

    </form>
</div>
@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    body,
    input,
    select,
    textarea,
    button {
        font-family: 'Inter', sans-serif !important
    }

    .bill-grid4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px 20px;
        align-items: start
    }

    .bill-grid3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px 20px;
        align-items: start
    }

    .info-dot {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 17px;
        height: 17px;
        background: var(--primary);
        color: #fff;
        border-radius: 50%;
        font-size: 10px;
        font-weight: 700;
        cursor: help;
        font-style: normal;
        vertical-align: middle;
        margin-left: 3px;
        flex-shrink: 0
    }

    /* additional expenses */
    /* Additional Expenses Styles */
    .expense-row {
        transition: background 0.2s;
    }

    .expense-row:hover {
        background: var(--primary-light) !important;
    }

    .expense-row td {
        padding: 10px 14px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .expense-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        transition: all 0.2s;
    }

    .expense-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .remove-expense-btn {
        background: none;
        border: 1px solid var(--danger);
        color: var(--danger);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        margin: 0 auto;
    }

    .remove-expense-btn:hover {
        background: var(--danger);
        color: #fff;
    }


    /* Job dropdown styles */
    .job-visual-option:hover {
        background: var(--primary-light) !important;
    }

    .job-visual-option.selected {
        background: var(--primary-light) !important;
    }

    .job-visual-option.selected .job-visual-check {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
    }

    .job-visual-option.selected .job-visual-check::after {
        content: '✓';
        font-size: 12px;
        color: #fff;
        font-weight: 700;
    }

    #billItemsBody tr td {
        padding: 7px 10px;
        border-bottom: 1px solid var(--border)
    }

    #billItemsBody input {
        font-size: 13px;
        padding: 5px 8px
    }

    #billItemSuggestions div:hover {
        background: var(--primary-light);
        cursor: pointer
    }

    @media(max-width:1100px) {
        .bill-grid4 {
            grid-template-columns: repeat(2, 1fr)
        }
    }

    @media(max-width:900px) {
        .bill-grid3 {
            grid-template-columns: repeat(2, 1fr)
        }
    }

    @media(max-width:600px) {

        .bill-grid4,
        .bill-grid3 {
            grid-template-columns: 1fr
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function generateBillItems(job, serviceCharge, percentage) {
        const category = (job.category || '').toLowerCase();
        const type = (job.type || '').toUpperCase();
        const qty = parseFloat(job.quantity) || 0;
        const items = [];

        //debugging
        console.log('Job Data:', {
            category: category,
            type: type,
            client: job.client_name,
            serviceCharge: serviceCharge
        });

        // 1. RULE: Import By Sea - FCL
        if (category.includes('import') && category.includes('sea') && type === 'FCL') {
            items.push({
                name: 'Documentation',
                desc: "Documentation Processing & Handling Charge",
                price: 1225
            });
            items.push({
                name: 'Copy B/L Noting Permission',
                desc: "Copy of Bill of Lading Noting Permission",
                price: 150
            });
            items.push({
                name: 'Port charge',
                desc: "Port Handling Charge",
            });
            items.push({
                name: 'Depot Charge Empty Container Payment',
                desc: "Depot Charge for Empty Container Payment"
            });
            items.push({
                name: 'Agent & NOC:',
                desc: "Agent & NOC Charges"
            });
            items.push({
                name: 'Labor sorting charge',
                desc: "Labor Sorting Charge",
            });
            items.push({
                name: 'Labor loading charge',
                desc: "Labor Loading Charge",
            });
            items.push({
                name: 'Labor unloading charge',
                desc: "Labor Unloading Charge",
            });
            items.push({
                name: 'Transportation',
                desc: "Transportation Charge",
            });
            items.push({
                name: 'Survey fee',
                desc: "Survey Fee",
                price: 110
            });
            items.push({
                name: 'Court fee',
                desc: "Court Fee"
            });
            items.push({
                name: 'Scanning & Vat charge',
                desc: "Scanning and VAT Charge"
            });
            items.push({
                name: 'Stamp',
                desc: "Stamp Duty"
            });
        }

        // 2. RULE: Import By Sea - LCL
        else if (category.includes('import') && category.includes('sea') && type === 'LCL') {
            items.push({
                name: 'Documentation',
                desc: "Documentation Processing & Handling Charge",
                price: 900
            });
            items.push({
                name: 'Copy B/L Noting Permission',
                desc: "Copy of Bill of Lading Noting Permission",
                price: 150
            });
            items.push({
                name: 'Agent & NOC Charges:',
                desc: "Agent and NOC Charges",
            });
            items.push({
                name: 'Port charge',
                desc: "Port Handling Charge",
            });
            // Labor charges: 4.17 x Quantity
            const laborAmt = (4.17 * qty).toFixed(2);
            items.push({
                name: 'Labor sorting charge',
                desc: `4.17 x ${qty} qty`,
                price: laborAmt
            });
            items.push({
                name: 'Labour loading charge',
                price: laborAmt,
                desc: `4.17 x ${qty} qty`
            });
            items.push({
                name: 'Transportation',
                desc: "Transportation Charge",
            });
            items.push({
                name: 'Court fee:',
                desc: "Court Fee",
            });
            items.push({
                name: 'Scanning & Vat Charge',
                desc: "Scanning and VAT Charge",
            });
        }

        // 3. RULE: Export by Air
        else if (category.includes('export') && category.includes('air')) {
            items.push({
                name: 'Documentation',
                price: 1000
            });
            items.push({
                name: 'Transport',
                desc: "Transportation Charge",
            });
            items.push({
                name: 'Court Fee',
                desc: "Court Fee",
                price: 66
            });
            items.push({
                name: 'Scanning & Vat Charge:',
                desc: "Scanning and VAT Charge",
            });
        }

        // 4. RULE: Export by Sea
        else if (category.includes('export') && category.includes('sea')) {
            items.push({
                name: 'Documentation',
                desc: "Documentation Processing & Handling Charge",
                price: 700
            });
            items.push({
                name: 'Off Dock Expenses',
                desc: "Off Dock Expenses Charge",
                price: 150
            });
            items.push({
                name: 'Court Fee',
                desc: "Court Fee",
                price: 66
            });
            items.push({
                name: 'River Dues',
                desc: "River Dues Charge"
            });
            items.push({
                name: 'Transport',
                desc: "Transportation Charge",
            });
            items.push({
                name: 'Scanning & Vat Charge:',
                desc: "Scanning and VAT Charge"
            });
        }

        // 5. RULE: By Truck (IMP)
        else if (category.includes('truck') && category.includes('imp')) {
            items.push({
                name: 'Documentation 0020',
                price: 500
            });
            items.push({
                name: 'DTI Charge 0043',
                price: 500
            });
        }

        // 5. RULE: By Truck (EXP)
        else if (category.includes('truck') && category.includes('exp')) {
            items.push({
                name: 'Documentation 0020',
                price: 500
            });
            items.push({
                name: 'DTI Charge 0043',
                price: 500
            });
        }

        // 6. RULE: Import by Air
        else if (category.includes('import') && category.includes('air')) {
            items.push({
                name: 'Documentation Processing & Handling Charge',
                price: 1575
            });
        }

        // 7. ALWAYS ADD: Agency Commission (Calculated Service Charge)
        if (serviceCharge > 0) {
            items.push({
                name: 'Agency Commission',
                price: serviceCharge,
                desc: `${percentage}% Service Charge for Job ${job.job_id}`
            });
        }

        // Now insert all these items into the main Bill table
        items.forEach(item => {
            addBillRow({
                name: item.name,
                description: item.desc || `Job: ${job.job_id || job.job_no}`,
                quantity: 1,
                price: item.price,
                jobId: job.id 
            });
        });
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // Job Dropdown with Search by Job ID or Client Name
    // ═══════════════════════════════════════════════════════════════════════════

    const floatDD = document.getElementById('jobFloatingDropdown');
    const trigger = document.getElementById('jobTrigger');
    const jobChevron = document.getElementById('jobChevron');
    const jobTags = document.getElementById('jobTags');
    let ddOpen = false;
    let addedJobsTracker = new Set(); // Track which jobs have service charges
    // Ensure a single global counter to avoid redeclaration errors
    window.expenseRowCounter = window.expenseRowCounter ?? 0;
    let expenseRowCounter = window.expenseRowCounter;

    // ─── DROPDOWN OPEN/CLOSE ──────────────────────────────────────────────
    function positionDD() {
        const r = trigger.getBoundingClientRect();
        floatDD.style.width = r.width + 'px';
        floatDD.style.left = r.left + 'px';
        const below = window.innerHeight - r.bottom;
        floatDD.style.top = below < 400 ? (r.top - 384) + 'px' : (r.bottom + 4) + 'px';
    }

    function openJobDropdown() {
        floatDD.style.display = 'flex';
        ddOpen = true;
        jobChevron.style.transform = 'rotate(180deg)';
        positionDD();
    }

    function closeJobDropdown() {
        floatDD.style.display = 'none';
        ddOpen = false;
        jobChevron.style.transform = 'rotate(0deg)';
    }

    function toggleJobDropdown(e) {
        if (e.target === document.getElementById('jobSearch')) return;
        ddOpen ? closeJobDropdown() : openJobDropdown();
    }

    document.addEventListener('click', e => {
        if (!trigger.contains(e.target) && !floatDD.contains(e.target)) closeJobDropdown();
    });

    ['scroll', 'resize'].forEach(ev =>
        window.addEventListener(ev, () => {
            if (ddOpen) positionDD();
        }, true)
    );

    // ─── JOB SELECTION HANDLER ────────────────────────────────────────────
    document.querySelectorAll('.job-visual-option').forEach(opt => {
        opt.addEventListener('click', function() {
            const id = this.dataset.id;
            const cb = document.querySelector(`#jobCheckboxPool .job-check[value="${id}"]`);
            if (!cb) return;

            cb.checked = !cb.checked;
            this.classList.toggle('selected', cb.checked);
            syncJobTags();
        });
    });

    // ─── SEARCH FILTER ────────────────────────────────────────────────────
    function filterJobs(val) {
        openJobDropdown();
        const v = val.toLowerCase().trim();
        let any = false;
        document.querySelectorAll('.job-visual-option').forEach(opt => {
            const match = !v || opt.dataset.search.includes(v);
            opt.style.display = match ? 'flex' : 'none';
            if (match) any = true;
        });
        document.getElementById('jobNoResults').style.display = any ? 'none' : 'block';
    }

    // ─── SYNC TAGS AND TRIGGER CALCULATION ────────────────────────────────
    function syncJobTags() {
        const checked = [...document.querySelectorAll('#jobCheckboxPool .job-check:checked')];
        jobTags.innerHTML = '';
        const ids = [];

        checked.forEach(cb => {
            ids.push(cb.value);

            const opt = document.querySelector(`.job-visual-option[data-id="${cb.value}"]`);
            if (opt) opt.classList.add('selected');

            // Create visual tag
            const tag = document.createElement('span');
            tag.style.cssText = `
            display:inline-flex;align-items:center;gap:6px;
            background:var(--primary-light);color:var(--primary);
            border:1px solid var(--primary);border-radius:20px;
            padding:4px 12px;font-size:12px;font-weight:600;
            font-family:'Inter',sans-serif
        `;
            tag.innerHTML = `
            <span>${cb.dataset.ref}</span>
            <span style="color:var(--text-muted);font-weight:400;font-size:11px">
                • ${cb.dataset.client || 'No client'}
            </span>
            <button type="button" onclick="removeJob('${cb.value}')"
                style="background:none;border:none;cursor:pointer;
                       color:var(--primary);font-size:16px;line-height:1;padding:0">
                ×
            </button>
        `;
            jobTags.appendChild(tag);
        });

        // Deselect visual rows that are unchecked
        document.querySelectorAll('.job-visual-option').forEach(opt => {
            if (!ids.includes(opt.dataset.id)) opt.classList.remove('selected');
        });

        // Update hidden input
        document.getElementById('jobNumberHidden').value = ids.join(',');


        // ★★★ NEW: AUTO-SET CLIENT FROM SELECTED JOB ★★★
        if (ids.length > 0 && !document.getElementById('clientSelect').value) {
            const firstCb = checked[0];
            const clientName = firstCb.dataset.client;
            if (clientName) {
                autoSelectClient(clientName);
            }
        }

        // ★★★ 1. Trigger service charge calculation (% based) for each NEW job ★★★
        ids.forEach(jobId => {
            if (!addedJobsTracker.has(jobId)) {
                fetchAndAddJobCharge(jobId);
            }
        });


        // ★★★ FIX 1: Remove auto-added bill items for jobs that were UNCHECKED ★★★
        document.querySelectorAll('.auto-bill-row').forEach(row => {
            if (!ids.includes(row.dataset.jobId)) {
                row.remove();
            }
        });

        // ★★★ 2. Remove service charges for jobs that were UNCHECKED ★★★
        document.querySelectorAll('.job-service-charge-row').forEach(row => {
            if (!ids.includes(row.dataset.jobId)) {
                addedJobsTracker.delete(row.dataset.jobId);
                row.remove();
            }
        });

        // Reset client when no jobs are selected
        if (ids.length === 0) {
            // 1. Clear the dropdown selection
            document.getElementById('clientSelect').value = '';

            // 2. Clear only the address display boxes
            const billingDiv = document.getElementById('billingAddress');
            const shippingDiv = document.getElementById('shippingAddress');

            if (billingDiv) billingDiv.innerHTML = '';
            if (shippingDiv) shippingDiv.innerHTML = '';

            // 3. Reset advance balance display
            const balanceEl = document.getElementById('advanceBalance');
            if (balanceEl) balanceEl.textContent = '0.00';

            // 4. Hide the warning balance text (if shown)
            const clientBalanceDiv = document.getElementById('clientBalance');
            if (clientBalanceDiv) clientBalanceDiv.style.display = 'none';

            // ★ DO NOT trigger the change event because it might hide the row ★
        }

        // ★★★ 3. NEW: Fetch Additional Expenses for selected jobs ★★★
        fetchAdditionalExpenses(ids);

        if (typeof renumberRows === 'function') renumberRows();
        if (typeof calculateExpenseTotal === 'function') calculateExpenseTotal();
        if (typeof calcBillTotals === 'function') calcBillTotals();
    }


    // ★★★ FIX 2: AUTO-SELECT CLIENT BASED ON JOB'S CLIENT NAME ★★★
    function autoSelectClient(clientName) {
        const clientSelect = document.getElementById('clientSelect');
        if (!clientSelect) return;

        // Loop through all client options and find a match
        let foundMatch = false;
        for (let option of clientSelect.options) {
            const optionText = option.text.toLowerCase();
            const targetName = clientName.toLowerCase().trim();

            // Match by business name (handles "(C001) Client Name" format)
            if (optionText.includes(targetName)) {
                clientSelect.value = option.value;
                foundMatch = true;

                // Trigger the change event so loadClientInfo() runs automatically
                const event = new Event('change');
                clientSelect.dispatchEvent(event);

                // Show notification
                showNotification(`✓ Auto-selected client: ${clientName}`);
                break;
            }
        }

        if (!foundMatch) {
            console.warn(`Client "${clientName}" not found in dropdown`);
        }
    }

    // Simple notification helper (in case it's not defined elsewhere)
    function showNotification(message) {
        const notif = document.createElement('div');
        notif.style.cssText = `
        position: fixed; top: 20px; right: 20px;
        background: var(--success); color: white;
        padding: 12px 20px; border-radius: 8px;
        font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600;
        box-shadow: 0 4px 12px rgba(16,185,129,0.3); z-index: 10000;
    `;
        notif.textContent = message;
        document.body.appendChild(notif);
        setTimeout(() => {
            notif.style.opacity = '0';
            notif.style.transition = 'opacity 0.3s';
            setTimeout(() => notif.remove(), 300);
        }, 2500);
    }

    // ─── FETCH AND ADD ADDITIONAL EXPENSES FROM JOBS ──────────────────────
    function fetchAdditionalExpenses(jobIds) {
        // Remove all previously added "tracked additional expense" rows first
        document.querySelectorAll('.tracked-additional-expense').forEach(row => row.remove());

        if (!jobIds || jobIds.length === 0) {
            if (typeof renumberExpenseRows === 'function') renumberExpenseRows();
            if (typeof calcBillTotals === 'function') calcBillTotals();
            return;
        }

        fetch(`/additional-expenses/get-by-jobs?job_ids=${jobIds.join(',')}`)
            .then(response => response.json())
            .then(data => {
                if (data.expenses && data.expenses.length > 0) {
                    data.expenses.forEach(exp => {
                        addTrackedAdditionalExpense(exp);
                    });
                }
                if (typeof renumberExpenseRows === 'function') renumberExpenseRows();
                if (typeof calcBillTotals === 'function') calcBillTotals();
            })
            .catch(error => {
                console.error('Error fetching additional expenses:', error);
            });
    }

    // ─── ADD TRACKED ADDITIONAL EXPENSE ROW ───────────────────────────────
    function addTrackedAdditionalExpense(exp) {
        expenseRowCounter++;
        const tbody = document.getElementById('expenseRowsBody');
        if (!tbody) return;

        const row = document.createElement('tr');
        row.className = 'expense-row tracked-additional-expense';
        row.id = `expenseRow_${expenseRowCounter}`;
        row.dataset.additionalExpenseId = exp.id;

        const desc = `${exp.description} (${exp.reference_no} - ${exp.job_label})`;

        row.innerHTML = `
        <td style="padding:10px 14px;border-bottom:1px solid var(--border);text-align:center;font-weight:600;color:var(--text-muted);font-size:13px">
            ${tbody.children.length + 1}
        </td>
        <td style="padding:10px 14px;border-bottom:1px solid var(--border)">
            <input type="text" 
                   name="additional_expenses[${expenseRowCounter}][description]" 
                   value="${desc}"
                   readonly
                   style="width:100%;padding:8px 12px;border:1px solid var(--border);
                          border-radius:var(--radius-sm);background:#dbeafe;
                          font-weight:600;color:#1e40af;font-family:'Inter',sans-serif;font-size:14px"
                   title="Auto-tracked from Additional Expenses module">
            <input type="hidden" name="additional_expenses[${expenseRowCounter}][additional_expense_id]" value="${exp.id}">
            <input type="hidden" name="additional_expenses[${expenseRowCounter}][job_id]" value="${exp.job_id}">
            <input type="hidden" name="additional_expenses[${expenseRowCounter}][is_auto]" value="1">
        </td>
        <td style="padding:10px 14px;border-bottom:1px solid var(--border)">
            <input type="number" 
                   name="additional_expenses[${expenseRowCounter}][amount]" 
                   class="expense-amount-input" 
                   value="${exp.to_be_billed}"
                   step="0.01" 
                   min="0"
                   oninput="calcBillTotals()"
                   style="width:100%;padding:8px 12px;border:1px solid var(--border);
                          border-radius:var(--radius-sm);background:#dbeafe;
                          font-weight:700;text-align:right;font-family:'Inter',sans-serif;font-size:14px"
                   required>
        </td>
        <td style="text-align:center;padding:10px 14px;border-bottom:1px solid var(--border)">
            <button type="button" 
                    onclick="this.closest('tr').remove(); calcBillTotals(); if(typeof renumberExpenseRows === 'function') renumberExpenseRows();"
                    style="background:none;border:1px solid var(--danger);color:var(--danger);
                           width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:18px;
                           display:flex;align-items:center;justify-content:center;margin:0 auto"
                    title="Remove this expense from bill">
                ×
            </button>
        </td>
    `;

        tbody.appendChild(row);

        // Hide the "no expenses" message
        const noExp = document.getElementById('noExpensesMessage');
        if (noExp) noExp.style.display = 'none';
    }

    function removeJob(id) {
        const cb = document.querySelector(`#jobCheckboxPool .job-check[value="${id}"]`);
        if (cb) {
            cb.checked = false;
            syncJobTags();
        }
    }

    function clearAllJobs() {
        document.querySelectorAll('#jobCheckboxPool .job-check').forEach(cb => cb.checked = false);
        document.querySelectorAll('.job-visual-option').forEach(opt => opt.classList.remove('selected'));
        syncJobTags();
        document.getElementById('jobSearch').value = '';
        filterJobs('');
    }

    // ─── FETCH JOB DATA AND ADD SERVICE CHARGE ROW ────────────────────────
    function fetchAndAddJobCharge(jobId) {
        console.log('Fetching job details for ID:', jobId); // DEBUG

        fetch(`/bills/get-job-details/${jobId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('API Response:', data); // DEBUG

                if (data.success && data.calculation.service_charge_amount > 0) {
                    addServiceChargeRow(data.job, data.calculation);
                    generateBillItems(data.job, data.service_charge, data.percentage);
                    addedJobsTracker.add(jobId);
                    showNotification(`✓ Applied structure for Job ${data.job.job_id}`);
                } else {
                    console.warn('No service charge to add. Calculation:', data.calculation);
                    showNotification('⚠ No service charge for Job ' + (data.job.job_id || data.job.job_no) + ' (Invoice value or category missing)', 'warning');
                }
            })
            .catch(error => {
                console.error('Error fetching job details:', error);
                showNotification('❌ Error: Could not fetch job details. Check console.', 'error');
            });
    }

    function addServiceChargeRow(job, calculation) {
        expenseRowCounter++;
        const tbody = document.getElementById('expenseRowsBody');

        if (!tbody) {
            console.error('expenseRowsBody not found! Make sure your additional expenses table exists.');
            return;
        }

        const row = document.createElement('tr');
        row.className = 'expense-row job-service-charge-row';
        row.id = `expenseRow_${expenseRowCounter}`;
        row.dataset.jobId = job.id;
        row.style.background = '#fef3c7';

        const jobLabel = job.job_id || job.job_no || ('Job #' + job.id);
        const description = `Agency Commission - ${jobLabel} (${calculation.percentage}% of $${parseFloat(calculation.imp_exp_value).toLocaleString()})`;

        row.innerHTML = `
        <td style="font-weight:600;color:var(--text-muted);font-size:13px;text-align:center;padding:10px 14px;border-bottom:1px solid var(--border)">
            ${tbody.children.length + 1}
        </td>
        <td style="padding:10px 14px;border-bottom:1px solid var(--border)">
            <input type="text" 
                   name="additional_expenses[${expenseRowCounter}][description]" 
                   value="${description}"
                   readonly
                   style="width:100%;padding:8px 12px;border:1px solid var(--border);
                          border-radius:var(--radius-sm);background:#fef3c7;
                          font-weight:600;color:#92400e;font-family:'Inter',sans-serif;font-size:14px">
            <input type="hidden" name="additional_expenses[${expenseRowCounter}][job_id]" value="${job.id}">
            <input type="hidden" name="additional_expenses[${expenseRowCounter}][is_auto]" value="1">
        </td>
        <td style="padding:10px 14px;border-bottom:1px solid var(--border)">
            <input type="number" 
                   name="additional_expenses[${expenseRowCounter}][amount]" 
                   class="expense-amount-input" 
                   value="${calculation.service_charge_amount}"
                   step="0.01" 
                   min="0"
                   oninput="calculateExpenseTotal()"
                   style="width:100%;padding:8px 12px;border:1px solid var(--border);
                          border-radius:var(--radius-sm);background:#fef3c7;
                          font-weight:700;text-align:right;font-family:'Inter',sans-serif;font-size:14px"
                   required>
        </td>
        <td style="text-align:center;padding:10px 14px;border-bottom:1px solid var(--border)">
            <button type="button" 
                    onclick="removeServiceChargeRow('${row.id}', '${job.id}')"
                    style="background:none;border:1px solid var(--danger);color:var(--danger);
                           width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:18px">
                ×
            </button>
        </td>
    `;

        tbody.appendChild(row);

        // Hide empty message
        const emptyMsg = document.getElementById('noExpensesMessage');
        if (emptyMsg) emptyMsg.style.display = 'none';

        // Recalculate
        if (typeof calculateExpenseTotal === 'function') calculateExpenseTotal();
        if (typeof renumberRows === 'function') renumberRows();

        showNotification(`✓ Added ৳${calculation.service_charge_amount.toFixed(2)} service charge for ${jobLabel}`, 'success');
    }

    function removeServiceChargeRow(rowId, jobId) {
        const row = document.getElementById(rowId);
        if (row) row.remove();
        addedJobsTracker.delete(jobId);

        if (typeof calculateExpenseTotal === 'function') calculateExpenseTotal();
        if (typeof renumberRows === 'function') renumberRows();

        // Also uncheck the job
        const cb = document.querySelector(`#jobCheckboxPool .job-check[value="${jobId}"]`);
        if (cb) {
            cb.checked = false;
            const opt = document.querySelector(`.job-visual-option[data-id="${jobId}"]`);
            if (opt) opt.classList.remove('selected');
            syncJobTags();
        }
    }

    function showNotification(message, type = 'success') {
        const colors = {
            success: 'var(--success)',
            warning: 'var(--warning)',
            error: 'var(--danger)'
        };

        const notif = document.createElement('div');
        notif.style.cssText = `
        position: fixed; top: 20px; right: 20px;
        background: ${colors[type] || colors.success};
        color: white; padding: 12px 20px;
        border-radius: var(--radius-sm);
        font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 10000;
    `;
        notif.textContent = message;
        document.body.appendChild(notif);

        setTimeout(() => {
            notif.style.opacity = '0';
            notif.style.transition = 'opacity 0.3s';
            setTimeout(() => notif.remove(), 300);
        }, 3000);
    }


    let billRowIdx = 0;
    let billSubTotal = 0;

    // Load client info
    function loadClientInfo(id) {
        if (!id) {
            document.getElementById('clientDetails').style.display = 'none';
            document.getElementById('clientBalance').style.display = 'none';
            return;
        }
        const opt = document.querySelector(`#clientSelect option[value="${id}"]`);
        if (!opt) return;
        const name = opt.dataset.name || '';
        const address = opt.dataset.address || '';
        const mobile = opt.dataset.mobile || '';
        const balance = parseFloat(opt.dataset.balance || 0);

        document.getElementById('billingAddress').innerHTML = `<strong>${name}</strong><br>${address}<br>Mobile: ${mobile}`;
        document.getElementById('shippingAddress').innerHTML = `<strong>${name}</strong><br>${address}`;
        document.getElementById('clientDetails').style.display = 'grid';

        const balEl = document.getElementById('clientBalance');
        if (balance > 0) {
            balEl.textContent = `Client: TK. ${balance.toLocaleString('en-BD', {minimumFractionDigits:2})}`;
            balEl.style.display = 'block';
        } else {
            balEl.style.display = 'none';
        }
    }

    // Add empty bill row

    let billRowCounter = 0;

    function addBillRow(itemData = {}) {
        billRowCounter++;
        const tbody = document.getElementById('billItemsBody');
        if (!tbody) return;

        const row = document.createElement('tr');
        row.className = 'item-row';
        row.id = `billRow_${billRowCounter}`;

        if (itemData.jobId) {
            row.classList.add('auto-bill-row');
            row.dataset.jobId = itemData.jobId;
        }

        // Set defaults from itemData or empty values
        const name = itemData.name || '';
        const desc = itemData.description || '';
        const qty = itemData.quantity || 1;
        const price = itemData.price || 0;

        row.innerHTML = `
        <td style="padding:10px 12px;text-align:center;border-bottom:1px solid var(--border);font-weight:600;color:var(--text-muted)">
            ${tbody.children.length + 1}
        </td>
        <td style="padding:10px 12px;border-bottom:1px solid var(--border)">
            <input type="text" name="item_name[]" value="${name}" class="form-control" required>
        </td>
        <td style="padding:10px 12px;border-bottom:1px solid var(--border)">
            <input type="text" name="description[]" value="${desc}" class="form-control">
        </td>
        <td style="padding:10px 12px;text-align:center;border-bottom:1px solid var(--border)">
            <input type="number" name="quantity[]" value="${qty}" step="0.01" class="form-control text-center" oninput="calcBillTotals()">
        </td>
        <td style="padding:10px 12px;text-align:right;border-bottom:1px solid var(--border)">
            <input type="number" name="unit_price[]" value="${price}" step="0.01" class="form-control text-right" oninput="calcBillTotals()">
            <input type="hidden" name="item_discount[]" value="0">
            <input type="hidden" name="item_tax[]" value="0">
        </td>
        <td style="padding:10px 12px;text-align:right;border-bottom:1px solid var(--border);font-weight:600">
            <span class="line-subtotal">0.00</span>
        </td>
        <td style="padding:10px 12px;text-align:center;border-bottom:1px solid var(--border)">
            <button type="button" onclick="this.closest('tr').remove(); calcBillTotals();" 
                style="background:none;border:1px solid var(--danger);color:var(--danger);width:28px;height:28px;border-radius:50%;cursor:pointer">
                ×
            </button>
        </td>
    `;

        tbody.appendChild(row);
        calcBillTotals();
    }

    function removeBillRow(i) {
        const tr = document.getElementById('brow-' + i);
        if (tr) tr.remove();
        calcBillTotals();
    }

    function attachBillRowListeners(i) {
        const row = document.getElementById('brow-' + i);
        const qty = row.querySelector('.brow-qty');
        const price = row.querySelector('.brow-price');
        const sub = row.querySelector('.brow-sub');

        function recalc() {
            const q = parseFloat(qty.value) || 0;
            const p = parseFloat(price.value) || 0;
            sub.value = (q * p).toFixed(2);
            calcBillTotals();
        }
        qty.addEventListener('input', recalc);
        price.addEventListener('input', recalc);
    }

    function calcBillTotals() {
        // 1. Items Subtotal
        let itemsSubTotal = 0;
        let totalItems = 0;

        document.querySelectorAll('#billItemsBody tr').forEach(row => {
            const qtyInput = row.querySelector('[name^="quantity"]');
            const priceInput = row.querySelector('[name^="unit_price"]');
            const discountInput = row.querySelector('[name^="item_discount"]');

            const qty = parseFloat(qtyInput?.value) || 0;
            const price = parseFloat(priceInput?.value) || 0;
            const discount = parseFloat(discountInput?.value) || 0;

            const lineTotal = (price - discount) * qty;
            itemsSubTotal += lineTotal;
            totalItems += qty;

            // Update line subtotal display in the row if it exists
            const subtotalCell = row.querySelector('.line-subtotal');
            if (subtotalCell) subtotalCell.textContent = lineTotal.toFixed(2);
        });

        // 2. Additional Expenses Total
        let additionalExpensesTotal = 0;
        document.querySelectorAll('.expense-amount-input').forEach(input => {
            additionalExpensesTotal += parseFloat(input.value) || 0;
        });

        // 3. Grand Total
        const grandTotal = itemsSubTotal + additionalExpensesTotal;

        // ─── UPDATE DISPLAYS (using YOUR exact IDs) ───

        // Items display
        const totalItemsDisp = document.getElementById('totalItemsDisp');
        if (totalItemsDisp) totalItemsDisp.textContent = totalItems.toFixed(2);

        const totalDisp = document.getElementById('totalDisp');
        if (totalDisp) totalDisp.textContent = itemsSubTotal.toFixed(2);

        // Additional expenses display
        const totalAdditionalExpenses = document.getElementById('totalAdditionalExpenses');
        if (totalAdditionalExpenses) totalAdditionalExpenses.textContent = additionalExpensesTotal.toFixed(2);

        const additionalExpensesTotalInput = document.getElementById('additionalExpensesTotal');
        if (additionalExpensesTotalInput) additionalExpensesTotalInput.value = additionalExpensesTotal.toFixed(2);

        // Grand total (Total Payable)
        const totalPayableDisp = document.getElementById('totalPayableDisp');
        if (totalPayableDisp) totalPayableDisp.textContent = grandTotal.toFixed(2);

        console.log('💰 Totals:', {
            items: itemsSubTotal.toFixed(2),
            additional: additionalExpensesTotal.toFixed(2),
            grandTotal: grandTotal.toFixed(2)
        });
    }

    function calculateExpenseTotal() {
        calcBillTotals(); // Just call the master function
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // ADDITIONAL EXPENSES (Manual Add Button)
    // ═══════════════════════════════════════════════════════════════════════════

    let expenseRowCounters = 0;

    function addExpenseRow() {
        expenseRowCounters++;
        const tbody = document.getElementById('expenseRowsBody');

        if (!tbody) {
            console.error('expenseRowsBody not found!');
            return;
        }

        const row = document.createElement('tr');
        row.className = 'expense-row manual-expense-row';
        row.id = `expenseRow_${expenseRowCounters}`;

        row.innerHTML = `
        <td style="padding:10px 14px;border-bottom:1px solid var(--border);text-align:center;font-weight:600;color:var(--text-muted);font-size:13px">
            ${tbody.children.length + 1}
        </td>
        <td style="padding:10px 14px;border-bottom:1px solid var(--border)">
            <input type="text" 
                   name="additional_expenses[${expenseRowCounters}][description]" 
                   placeholder="e.g., Transportation, Customs Fee, Loading Charge"
                   style="width:100%;padding:8px 12px;border:1px solid var(--border);
                          border-radius:var(--radius-sm);font-family:'Inter',sans-serif;font-size:14px"
                   required>
        </td>
        <td style="padding:10px 14px;border-bottom:1px solid var(--border)">
            <input type="number" 
                   name="additional_expenses[${expenseRowCounters}][amount]" 
                   class="expense-amount-input"
                   placeholder="0.00" 
                   step="0.01" 
                   min="0"
                   oninput="calcBillTotals()"
                   style="width:100%;padding:8px 12px;border:1px solid var(--border);
                          border-radius:var(--radius-sm);text-align:right;
                          font-family:'Inter',sans-serif;font-size:14px;font-weight:600"
                   required>
        </td>
        <td style="text-align:center;padding:10px 14px;border-bottom:1px solid var(--border)">
            <button type="button" 
                    onclick="removeExpenseRow('expenseRow_${expenseRowCounters}')"
                    style="background:none;border:1px solid var(--danger);color:var(--danger);
                           width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:18px;
                           display:flex;align-items:center;justify-content:center;margin:0 auto">
                ×
            </button>
        </td>
    `;

        tbody.appendChild(row);

        // Hide empty message
        const emptyMsg = document.getElementById('noExpensesMessage');
        if (emptyMsg) emptyMsg.style.display = 'none';

        // Focus the description input
        row.querySelector('input[type="text"]').focus();

        calcBillTotals();
        renumberExpenseRows();
    }

    function removeExpenseRow(rowId) {
        const row = document.getElementById(rowId);
        if (!row) return;

        // If it was an auto job charge, untrack it
        if (row.classList.contains('job-service-charge-row') && row.dataset.jobId) {
            addedJobsTracker.delete(row.dataset.jobId);

            // Also uncheck the job in the dropdown
            const cb = document.querySelector(`#jobCheckboxPool .job-check[value="${row.dataset.jobId}"]`);
            if (cb) {
                cb.checked = false;
                const opt = document.querySelector(`.job-visual-option[data-id="${row.dataset.jobId}"]`);
                if (opt) opt.classList.remove('selected');
                // Refresh tags
                if (typeof syncJobTags === 'function') syncJobTags();
            }
        }

        row.remove();

        // Show empty message if no rows left
        const tbody = document.getElementById('expenseRowsBody');
        if (tbody && tbody.children.length === 0) {
            const emptyMsg = document.getElementById('noExpensesMessage');
            if (emptyMsg) emptyMsg.style.display = 'block';
        }

        calcBillTotals();
        renumberExpenseRows();
    }

    function renumberExpenseRows() {
        document.querySelectorAll('#expenseRowsBody tr').forEach((row, index) => {
            const firstCell = row.querySelector('td:first-child');
            if (firstCell) firstCell.textContent = index + 1;
        });
    }

    // Item search
    let billSearchTimeout;

    function searchBillItems(val) {
        clearTimeout(billSearchTimeout);
        const box = document.getElementById('billItemSuggestions');
        if (!val.trim()) {
            box.style.display = 'none';
            return;
        }
        billSearchTimeout = setTimeout(() => {
            fetch(`/bills/items/search?q=${encodeURIComponent(val)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.length) {
                        box.style.display = 'none';
                        return;
                    }
                    box.innerHTML = '';
                    data.forEach(item => {
                        const d = document.createElement('div');
                        d.style.cssText = 'padding:9px 14px;font-size:13px;border-bottom:1px solid var(--border)';
                        d.textContent = item.item_name + (item.item_code ? ' ' + item.item_code : '');
                        d.addEventListener('click', () => {
                            addBillRow({
                                item_name: item.item_name,
                                item_code: item.item_code,
                                unit: item.unit,
                                price: item.billing_exc_tax || 0,
                                qty: 1
                            });
                            document.getElementById('billItemSearch').value = '';
                            box.style.display = 'none';
                        });
                        box.appendChild(d);
                    });
                    box.style.display = 'block';
                });
        }, 250);
    }
    document.addEventListener('click', e => {
        if (!e.target.closest('#billItemSearch') && !e.target.closest('#billItemSuggestions'))
            document.getElementById('billItemSuggestions').style.display = 'none';
    });
</script>
@endpush