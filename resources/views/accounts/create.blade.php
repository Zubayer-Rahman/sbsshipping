@extends('layouts.app')
@section('title','Create Account')
@section('page-title','Create Payment Account')
@section('breadcrumb','Accounts / Create')

@section('content')
<style>
    .create-container {
        padding: 2rem;
        max-width: 800px;
        margin: 0 auto;
        font-family: 'Inter', sans-serif;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .back-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .form-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 2rem;
        box-shadow: var(--shadow-md);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
        font-weight: 500;
    }

    .required {
        color: var(--danger);
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        font-family: 'Inter', sans-serif;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .form-control.error {
        border-color: var(--danger);
    }

    .error-text {
        margin-top: 0.375rem;
        font-size: 0.875rem;
        color: var(--danger);
    }

    .help-text {
        margin-top: 0.375rem;
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .account-type-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .account-type-option {
        padding: 1rem;
        border: 2px solid var(--border);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
    }

    .account-type-option:hover {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .account-type-option input[type="radio"] {
        display: none;
    }

    .account-type-option input[type="radio"]:checked+.type-content {
        color: var(--primary);
    }

    .account-type-option input[type="radio"]:checked~.account-type-option {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .type-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .type-name {
        font-weight: 600;
        font-size: 0.938rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-sm);
        font-weight: 500;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        flex: 1;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        box-shadow: 0 4px 12px var(--primary-glow);
    }

    .btn-secondary {
        background: var(--text-muted);
        color: white;
    }

    .btn-secondary:hover {
        background: #475569;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }
</style>

<div class="create-container">
    <div class="page-header">
        <h1 class="page-title">Create Payment Account</h1>
        <a href="{{ route('accounts.index') }}" class="back-link">← Back to Accounts</a>
    </div>

    <div class="form-card">
        <form action="{{ route('accounts.store') }}" method="POST">
            @csrf
            <!-- Account Name -->
            <div class="form-group">
                <label class="form-label">Account Name <span class="required">*</span></label>
                <input type="text" name="account_name" value="{{ old('account_name') }}"
                    required class="form-control @error('account_name') error @enderror"
                    placeholder="e.g., Cash In Hand, DBBL Main Account">
                @error('account_name')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Account Type -->
            <!-- <div class="form-group">
                <label class="form-label">Account Type <span class="required">*</span></label>
                <div class="account-type-grid">
                    <label class="account-type-option">
                        <input type="radio" name="account_type" value="bank" {{ old('account_type') == 'bank' ? 'checked' : '' }} required>
                        <div class="type-content">
                            <div class="type-icon">🏦</div>
                            <div class="type-name">Bank Account</div>
                        </div>
                    </label>
                    <label class="account-type-option">
                        <input type="radio" name="account_type" value="cash" {{ old('account_type') == 'cash' ? 'checked' : '' }} required>
                        <div class="type-content">
                            <div class="type-icon">💵</div>
                            <div class="type-name">Cash in Hand</div>
                        </div>
                    </label>
                    <label class="account-type-option">
                        <input type="radio" name="account_type" value="mobile_banking" {{ old('account_type') == 'mobile_banking' ? 'checked' : '' }} required>
                        <div class="type-content">
                            <div class="type-icon">📱</div>
                            <div class="type-name">Mobile Banking</div>
                        </div>
                    </label>
                    <label class="account-type-option">
                        <input type="radio" name="account_type" value="other" {{ old('account_type') == 'other' ? 'checked' : '' }} required>
                        <div class="type-content">
                            <div class="type-icon">💳</div>
                            <div class="type-name">Other</div>
                        </div>
                    </label>
                </div>
                @error('account_type')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div> -->

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <!-- Account Number -->
                <div class="form-group">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="account_number" value="{{ old('account_number') }}"
                        class="form-control @error('account_number') error @enderror"
                        placeholder="e.g., 1234567890">
                    @error('account_number')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Opening Balance -->
                <div class="form-group">
                    <label class="form-label">Opening Balance <span class="required">*</span></label>
                    <input type="number" name="opening_balance" step="0.01" min="0" value="{{ old('opening_balance', 0) }}"
                        required class="form-control @error('opening_balance') error @enderror">
                    @error('opening_balance')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <!-- Bank Name -->
                <div class="form-group">
                    <label class="form-label">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                        class="form-control @error('bank_name') error @enderror"
                        placeholder="e.g., Dutch Bangla Bank">
                    <p class="help-text">For bank accounts only</p>
                    @error('bank_name')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Branch -->
                <div class="form-group">
                    <label class="form-label">Branch</label>
                    <input type="text" name="branch" value="{{ old('branch') }}"
                        class="form-control @error('branch') error @enderror"
                        placeholder="e.g., Gulshan Branch">
                    @error('branch')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') error @enderror"
                    placeholder="Optional notes about this account">{{ old('description') }}</textarea>
                @error('description')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Account</button>
                <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection