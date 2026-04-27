@extends('layouts.app')

@section('content')
<style>
    .release-container {
        padding: 2rem;
        max-width: 900px;
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

    .info-card {
        background: var(--primary-light);
        border-left: 4px solid var(--primary);
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .info-item-label {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
        font-family: 'Inter', sans-serif;
    }

    .info-item-value {
        font-weight: 600;
        color: var(--text-primary);
        font-family: 'Inter', sans-serif;
    }

    .form-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 2rem;
        box-shadow: var(--shadow-md);
    }

    .form-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--border);
        font-family: 'Inter', sans-serif;
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

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
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

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid var(--border);
    }

    .btn {
        padding: 0.875rem 2rem;
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

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        background: #059669;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .btn-secondary {
        background: var(--text-muted);
        color: white;
    }

    .btn-secondary:hover {
        background: #475569;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="release-container">
    <div class="page-header">
        <h1 class="page-title">
            Release IOU
            @if($iou->type == 'receivable')
            <span style="color: var(--success); font-size: 1rem;">(Money Received)</span>
            @else
            <span style="color: var(--danger); font-size: 1rem;">(Money Paid)</span>
            @endif
        </h1>
        <a href="{{ route('ious.show', $iou) }}" class="back-link">← Back to IOU</a>
    </div>

    <!-- IOU Information Card -->
    <div class="info-card">
        <div class="info-grid">
            <div class="info-item">
                <p class="info-item-label">IOU Reference</p>
                <p class="info-item-value">{{ $iou->reference_number }}</p>
            </div>
            <div class="info-item">
                <p class="info-item-label">Type</p>
                <p class="info-item-value">
                    @if($iou->type == 'receivable')
                    <span style="color: var(--success); font-weight: 600;">Receivable (They Owe You)</span>
                    @else
                    <span style="color: var(--danger); font-weight: 600;">Payable (You Owe Them)</span>
                    @endif
                </p>
            </div>
            <div class="info-item">
                <p class="info-item-label">Contact</p>
                <p class="info-item-value">{{ $iou->contact->name }}</p>
            </div>
            <div class="info-item">
                <p class="info-item-label">Total Amount</p>
                <p class="info-item-value">৳{{ number_format($iou->amount, 2) }}</p>
            </div>
            <div class="info-item">
                <p class="info-item-label">Already Paid</p>
                <p class="info-item-value">৳{{ number_format($iou->paid_amount, 2) }}</p>
            </div>
            <div class="info-item">
                <p class="info-item-label">Remaining Balance</p>
                <p class="info-item-value" style="color: var(--danger); font-weight: 600;">৳{{ number_format($iou->balance, 2) }}</p>
            </div>
            @if($iou->against)
            <div class="info-item">
                <p class="info-item-label">Against</p>
                <p class="info-item-value">{{ $iou->against }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Release Form -->
    <div class="form-card">
        <h3 class="form-section-title">Expense Details</h3>

        <form action="{{ route('ious.process-release', $iou) }}" method="POST" enctype="multipart/form-data" id="release-form">
            @csrf

            <div class="form-row">
                <!-- Expense Category -->
                <div class="form-group">
                    <label class="form-label">Expense Category <span class="required">*</span></label>
                    <select name="expense_category_id" id="expense_category_id" required
                        class="form-control @error('expense_category_id') error @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('expense_category_id')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sub Category -->
                <div class="form-group">
                    <label class="form-label">Sub Category</label>
                    <input type="text" name="sub_category" value="{{ old('sub_category') }}"
                        class="form-control @error('sub_category') error @enderror">
                    @error('sub_category')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <!-- Job Number -->
                <div class="form-group">
                    <label class="form-label">Job Number</label>
                    <select name="job_id" id="job_id" class="form-control @error('job_id') error @enderror">
                        <option value="">Select Job</option>
                        @foreach($jobs as $job)
                        <option value="{{ $job->id }}" {{ old('job_id') == $job->id ? 'selected' : '' }}>
                            {{ $job->id }} - {{ $job->title ?? $job->description ?? 'Job' }}
                        </option>
                        @endforeach
                    </select>
                    <p class="help-text">Disabled when Office Expense is selected</p>
                    @error('job_id')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expense Date -->
                <div class="form-group">
                    <label class="form-label">Release Date <span class="required">*</span></label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}"
                        required class="form-control @error('expense_date') error @enderror">
                    @error('expense_date')
                    <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Expense Contact (Auto-filled) -->
            <div class="form-group">
                <label class="form-label">Expense Contact</label>
                <input type="text" value="{{ $iou->contact->name }}" disabled class="form-control">
                <p class="help-text">Auto-filled from IOU contact</p>
            </div>

            <!-- Expenses For -->
            <div class="form-group">
                <label class="form-label">Expenses For <span class="required">*</span></label>
                <input type="text" name="expenses_for" value="{{ old('expenses_for', $iou->description) }}"
                    required class="form-control @error('expenses_for') error @enderror">
                @error('expenses_for')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Client Name -->
            <div class="form-group">
                <label class="form-label">Client Name</label>
                <input type="text" name="client_name" value="{{ old('client_name') }}"
                    class="form-control @error('client_name') error @enderror"
                    placeholder="For which client this expense was made">
                @error('client_name')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div class="form-group">
                <label class="form-label">Amount <span class="required">*</span></label>
                <input type="number" name="amount" step="0.01" min="0.01"
                    max="{{ $iou->balance }}"
                    required class="form-control @error('amount') error @enderror">
                <p class="help-text">
                    Maximum releasable amount: ৳{{ number_format($iou->balance, 2) }}
                    @if($iou->type == 'receivable')
                    (Money you're receiving from {{ $iou->contact->name }})
                    @else
                    (Money you're paying to {{ $iou->contact->name }})
                    @endif
                </p>
                @error('amount')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Supporting Document -->
            <div class="form-group">
                <label class="form-label">Supporting Document</label>
                @if($iou->document)
                <div style="margin-bottom: 0.75rem;">
                    <a href="{{ Storage::url($iou->document) }}" target="_blank"
                        style="color: var(--primary); text-decoration: none; font-weight: 500;">
                        📄 View IOU Document
                    </a>
                </div>
                @endif
                <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png"
                    class="form-control @error('document') error @enderror">
                <p class="help-text">PDF, JPG, PNG (Max 2MB). Optional - can use IOU document</p>
                @error('document')
                <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Release IOU & Add to Expenses</button>
                <a href="{{ route('ious.show', $iou) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Disable Job Number field when "Office Expense" is selected
    document.getElementById('expense_category_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex].text.toLowerCase();
        const jobField = document.getElementById('job_id');

        if (selectedOption.includes('office')) {
            jobField.disabled = true;
            jobField.value = '';
        } else {
            jobField.disabled = false;
        }
    });

    // Trigger on page load if there's an old value
    window.addEventListener('DOMContentLoaded', function() {
        document.getElementById('expense_category_id').dispatchEvent(new Event('change'));
    });
</script>
@endsection