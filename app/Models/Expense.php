<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'expense_ref', 'business_location', 'expense_category', 'sub_category',
        'job_id', 'job_ref_no', 'expense_date', 'expense_for', 'expense_for_contact',
        'applicable_tax', 'total_amount', 'is_refund', 'document_path', 'expense_note',
        'is_recurring', 'recurring_interval', 'recurring_interval_type', 'no_of_repetitions',
        'payment_amount', 'paid_on', 'payment_method', 'payment_account', 'payment_note',
        'payment_status', 'payment_due', 'user_id', 'added_by',
    ];

    protected $casts = [
        'expense_date'  => 'datetime',
        'paid_on'       => 'datetime',
        'total_amount'  => 'decimal:2',
        'payment_amount'=> 'decimal:2',
        'payment_due'   => 'decimal:2',
        'is_refund'     => 'boolean',
        'is_recurring'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Auto-generate ref before creating
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($expense) {
            if (empty($expense->expense_ref)) {
                $count = static::count() + 1;
                $expense->expense_ref = 'EP' . date('Y') . '/' . $count;
            }
            // Calculate payment due
            $expense->payment_due = $expense->total_amount - $expense->payment_amount;
            if ($expense->payment_due <= 0) {
                $expense->payment_status = 'Paid';
                $expense->payment_due    = 0;
            } elseif ($expense->payment_amount > 0) {
                $expense->payment_status = 'Partial';
            } else {
                $expense->payment_status = 'Due';
            }
        });
    }
}