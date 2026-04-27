@extends('layouts.app')
@section('title','Edit IOU')
@section('page-title','Edit IOU')
@section('breadcrumb','IOUs / Edit IOU')

@section('content')
<style>
    .iou-edit-container {
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
        color: var(--text-muted);
    }

    .help-text {
        margin-top: 0.375rem;
        font-size: 0.875rem;
        color: var(--text-muted);
        font-family: 'Inter', sans-serif;
    }

    .warning-text {
        margin-top: 0.375rem;
        font-size: 0.875rem;
        color: var(--warning);
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

    .radio-option.selected {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .radio-option input[type="radio"] {
        margin-right: 0.75rem;
        width: 18px;
        height: 18px;
        cursor: pointer;
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

    .current-document {
        margin-bottom: 0.75rem;
        padding: 0.75rem;
        background: var(--primary-light);
        border-radius: var(--radius-sm);
    }

    .document-link {
        color: var(--primary);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        font-weight: 500;
        transition: color 0.2s;
    }

    .document-link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
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

<div class="iou-edit-container">
    <div class="page-header">
        <h1 class="page-title">Edit IOU</h1>
        <a href="{{ route('ious.show', $iou) }}" class="back-link">← Back to IOU</a>
    </div>

    <div class="form-card">
        <form action="{{ route('ious.update', $iou) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Reference Number -->
            <div class="form-group">
                <label class="form-label">Reference Number</label>
                <input type="text" value="{{ $iou->reference_number }}" disabled class="form-control">
            </div>

            <!-- Contact -->
            <div class="form-group">
                <label class="form-label">Contact <span class="required">*</span></label>
                <select name="contact_id" required class="form-control @error('contact_id') error @enderror">
                    <option value="">Select Contact</option>
                    @foreach($contacts as $contact)
                    <option value="{{ $contact->id }}" {{ (old('contact_id', $iou->contact_id) == $contact->id) ? 'selected' : '' }}>
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
                    <label class="radio-option receivable {{ old('type', $iou->type) == 'receivable' ? 'selected' : '' }}">
                        <input type="radio" name="type" value="receivable"
                            {{ old('type', $iou->type) == 'receivable' ? 'checked' : '' }} required>
                        <div class="radio-content">
                            <div class="radio-content-title">Receivable</div>
                            <div class="radio-content-desc">They owe you</div>
                        </div>
                    </label>
                    <label class="radio-option payable {{ old('type', $iou->type) == 'payable' ? 'selected' : '' }}">
                        <input type="radio" name="type" value="payable"
                            {{ old('type', $iou->type) == 'payable' ? 'checked' : '' }} required>
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
                <input type="number" name="amount" step="0.01" min="0.01"
                    value="{{ old('amount', $iou->amount) }}"
                    required class="form-control @error('amount') error @enderror">
                @if($iou->paid_amount > 0)
                <p class="warning-text">⚠️ ৳{{ number_format($iou->paid_amount, 2) }} has already been paid</p>
                @endif
                @error('amount')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Against -->
            <div class="form-group">
                <label class="form-label">Against</label>
                <input type="text" name="against" value="{{ old('against', $iou->against) }}"
                    placeholder="e.g., Job #123, Purchase Order, Personal Loan"
                    class="form-control @error('against') error @enderror">
                @error('against')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') error @enderror">{{ old('description', $iou->description) }}</textarea>
                @error('description')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Due Date -->
            <div class="form-group">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date"
                    value="{{ old('due_date', $iou->due_date ? $iou->due_date->format('Y-m-d') : '') }}"
                    class="form-control @error('due_date') error @enderror">
                @error('due_date')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Document -->
            <div class="form-group">
                <label class="form-label">Supporting Document</label>
                @if($iou->document)
                <div class="current-document">
                    <a href="{{ Storage::url($iou->document) }}" target="_blank" class="document-link">
                        📄 Current Document
                    </a>
                </div>
                @endif
                <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"
                    class="form-control @error('document') error @enderror">
                <p class="help-text">PDF, JPG, PNG (Max 2MB). Leave empty to keep current document.</p>
                @error('document')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update IOU</button>
                <a href="{{ route('ious.show', $iou) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Add visual feedback for radio selection
    document.querySelectorAll('.radio-option input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.radio-option').forEach(option => {
                option.classList.remove('selected');
            });
            if (this.checked) {
                this.closest('.radio-option').classList.add('selected');
            }
        });
    });
</script>
@endsection