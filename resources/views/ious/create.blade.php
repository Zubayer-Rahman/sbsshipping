@extends('layouts.app')

@section('content')
<style>
    .iou-create-container {
        padding: 2rem;
        max-width: 800px;
        margin: 0 auto;
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
        font-family: 'Inter', sans-serif;
    }

    .back-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
        font-family: 'Inter', sans-serif;
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
        font-family: 'Inter', sans-serif;
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
        font-size: 0.938rem;
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

    .form-control:disabled {
        background: var(--body-bg);
        cursor: not-allowed;
    }

    .help-text {
        margin-top: 0.375rem;
        font-size: 0.875rem;
        color: var(--text-muted);
        font-family: 'Inter', sans-serif;
    }

    .error-text {
        margin-top: 0.375rem;
        font-size: 0.875rem;
        color: var(--danger);
        font-family: 'Inter', sans-serif;
    }

    .radio-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .radio-option {
        padding: 1rem;
        border: 2px solid var(--border);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
    }

    .radio-option:hover {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .radio-option input[type="radio"] {
        margin-right: 0.75rem;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .radio-option input[type="radio"]:checked+.radio-content {
        color: var(--primary);
    }

    .radio-content-title {
        font-weight: 600;
        font-family: 'Inter', sans-serif;
    }

    .radio-content-desc {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-family: 'Inter', sans-serif;
    }

    .receivable .radio-content-title {
        color: var(--success);
    }

    .payable .radio-content-title {
        color: var(--danger);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
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
        font-family: 'Inter', sans-serif;
        text-decoration: none;
        display: inline-block;
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
</style>

<div class="iou-create-container">
    <div class="page-header">
        <h1 class="page-title">Create New IOU</h1>
        <a href="{{ route('ious.index') }}" class="back-link">← Back to List</a>
    </div>

    <div class="form-card">
        <form action="{{ route('ious.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Reference Number -->
            <div class="form-group">
                <label class="form-label">Reference Number</label>
                <input type="text" value="{{ $referenceNumber }}" disabled class="form-control">
                <p class="help-text">Auto-generated</p>
            </div>

            <!-- Contact -->
            <div class="form-group">
                <label class="form-label">Contact <span class="required">*</span></label>
                <select name="contact_id" required class="form-control @error('contact_id') error @enderror">
                    <option value="">Select Contact</option>
                    @foreach($contacts as $contact)
                    <option value="{{ $contact->id }}" {{ old('contact_id') == $contact->id ? 'selected' : '' }}>
                        {{ $contact->name }}
                    </option>
                    @endforeach
                </select>
                @error('contact_id')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div class="form-group">
                <label class="form-label">Type <span class="required">*</span></label>
                <div class="radio-group">
                    <label class="radio-option receivable">
                        <input type="radio" name="type" value="receivable" {{ old('type') == 'receivable' ? 'checked' : '' }} required>
                        <div class="radio-content">
                            <div class="radio-content-title">Receivable</div>
                            <div class="radio-content-desc">They owe you</div>
                        </div>
                    </label>
                    <label class="radio-option payable">
                        <input type="radio" name="type" value="payable" {{ old('type') == 'payable' ? 'checked' : '' }} required>
                        <div class="radio-content">
                            <div class="radio-content-title">Payable</div>
                            <div class="radio-content-desc">You owe them</div>
                        </div>
                    </label>
                </div>
                @error('type')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div class="form-group">
                <label class="form-label">Amount <span class="required">*</span></label>
                <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}"
                    required class="form-control @error('amount') error @enderror">
                @error('amount')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Against -->
            <div class="form-group">
                <label class="form-label">Against</label>
                <input type="text" name="against" value="{{ old('against') }}"
                    placeholder="e.g., Job #123, Purchase Order, Personal Loan"
                    class="form-control @error('against') error @enderror">
                @error('against')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') error @enderror">{{ old('description') }}</textarea>
                @error('description')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Due Date -->
            <div class="form-group">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date') }}"
                    class="form-control @error('due_date') error @enderror">
                @error('due_date')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Document -->
            <div class="form-group">
                <label class="form-label">Supporting Document</label>
                <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"
                    class="form-control @error('document') error @enderror">
                <p class="help-text">PDF, JPG, PNG (Max 2MB)</p>
                @error('document')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create IOU</button>
                <a href="{{ route('ious.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection