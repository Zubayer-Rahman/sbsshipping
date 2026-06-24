@extends('layouts.app')
@section('title', 'Add Additional Expense')
@section('content')

<div style="padding:2rem;max-width:800px;margin:0 auto;font-family:'Inter',sans-serif">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
        <h1 style="font-size:1.75rem;font-weight:800;color:var(--text-primary);margin:0">
            New Additional Expense
        </h1>
        <a href="{{ route('additional-expenses.index') }}"
            style="color:var(--primary);text-decoration:none;font-weight:600">← Back to List</a>
    </div>

    <div style="background:#fff;padding:32px;border-radius:12px;box-shadow:var(--shadow-md)">
        <form action="{{ route('additional-expenses.store') }}" method="POST">
            @csrf

            {{-- Reference --}}
            <div style="margin-bottom:20px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                    Reference Number
                </label>
                <input type="text" value="{{ $referenceNo }}" disabled
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;
                              background:var(--body-bg);font-size:14px">
            </div>

            {{-- Client + Job Row --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                        Client <span style="color:var(--danger)">*</span>
                    </label>
                    <select name="client_id" required
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->business_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                        Job
                    </label>
                    <select name="job_id"
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
                        <option value="">Select Job (Optional)</option>
                        @foreach($jobs as $job)
                        <option value="{{ $job->no }}" {{ old('job_no') == $job->no ? 'selected' : '' }}>
                            {{ $job->job_no ?? 'Job #'.$job->no }} - {{ $job->client_name ?? 'No client' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Description --}}
            <div style="margin-bottom:20px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                    Description <span style="color:var(--danger)">*</span>
                </label>
                <input type="text" name="description" value="{{ old('description') }}" required
                    placeholder="e.g., Extra customs clearance fee, Transportation cost"
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">


                <div class="form-group">
                    <label class="form-label">Payment Account:</label>
                    <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                        <span style="padding:0 10px;border-right:1px solid var(--border);height:40px;display:flex;align-items:center">
                            <i class="bi bi-cash-stack" style="color:var(--text-muted)"></i>
                        </span>
                        <select name="payment_account_id" required class="form-control">
                            @foreach(\App\Models\PaymentAccount::where('is_active', true)->orderBy('account_name')->get() as $acc)
                            <option value="{{ $acc->id }}" {{ old('payment_account_id') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->account_name }} (Balance: ৳{{ number_format($acc->current_balance, 2) }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Amounts Row --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                        Actual Amount <span style="color:var(--danger)">*</span>
                    </label>
                    <input type="number" name="actual_amount" value="{{ old('actual_amount', 0) }}" step="0.01" min="0" required
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
                    <small style="color:var(--text-muted);font-size:11px">What you actually paid</small>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                        To Be Billed <span style="color:var(--danger)">*</span>
                    </label>
                    <input type="number" name="to_be_billed" value="{{ old('to_be_billed', 0) }}" step="0.01" min="0" required
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
                    <small style="color:var(--text-muted);font-size:11px">What you'll charge the client</small>
                </div>
            </div>

            {{-- Date --}}
            <div style="margin-bottom:20px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                    Expense Date <span style="color:var(--danger)">*</span>
                </label>
                <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px">
            </div>

            {{-- Notes --}}
            <div style="margin-bottom:24px">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px">
                    Notes
                </label>
                <textarea name="notes" rows="3"
                    style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px;resize:vertical">{{ old('notes') }}</textarea>
            </div>

            {{-- Submit --}}
            <div style="display:flex;gap:12px">
                <button type="submit"
                    style="flex:1;padding:12px;background:var(--primary);color:#fff;border:none;
                               border-radius:6px;font-weight:600;cursor:pointer;font-size:14px">
                    Save Additional Expense
                </button>
                <a href="{{ route('additional-expenses.index') }}"
                    style="padding:12px 24px;background:#64748b;color:#fff;border-radius:6px;
                          text-decoration:none;font-weight:600;font-size:14px">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection