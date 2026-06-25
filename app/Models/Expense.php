<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'expense_ref',
        'business_location',
        'expense_category',
        'sub_category',
        'job_id',
        'job_no',
        'job_ref_no',
        'expense_date',
        'expense_for',
        'expense_for_contact',
        'applicable_tax',
        'total_amount',
        'is_refund',
        'document_path',
        'expense_note',
        'is_recurring',
        'recurring_interval',
        'recurring_interval_type',
        'no_of_repetitions',
        'payment_amount',
        'paid_on',
        'payment_method',
        'payment_account',
        'payment_note',
        'payment_status',
        'payment_due',
        'user_id',
        'added_by',
        'payment_account_id',
    ];

    protected $casts = [
        'expense_date'  => 'datetime',
        'paid_on'       => 'datetime',
        'total_amount'  => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'payment_due'   => 'decimal:2',
        'is_refund'     => 'boolean',
        'is_recurring'  => 'boolean',
    ];
    
    public function jobs()
    {
        return $this->belongsToMany(
            Job::class,
            'expense_job',
            'expense_id',
            'job_id',
            'id',    // ← must be 'id', not 'job_no' or anything else
            'id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        // Make sure 'expense_category_id' matches the column name in your expenses table
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
    public function expenseJobs()
    {
        return $this->hasMany(ExpenseJob::class, 'expense_id');
    }

    public function linkedJobs()
    {
        return Job::whereIn('id', $this->expenseJobs()->pluck('job_id'));
    }

    public function attachToJob($jobId)
    {
        $expenseId = $this->id ?? ExpenseJob::max('expense_id') + 1; // Fallback

        return ExpenseJob::firstOrCreate([
            'expense_id' => $expenseId,
            'job_id'     => $jobId,
        ]);
    }

    /**
     * Helper: Detach expense from a job
     */
    public function detachFromJob($jobId)
    {
        return ExpenseJob::where('expense_id', $this->id)
            ->where('job_id', $jobId)
            ->delete();
    }

    /**
     * Helper: Sync jobs (remove all, add new)
     */
    public function syncJobs($jobIds = [])
    {
        ExpenseJob::where('expense_id', $this->id)->delete();

        foreach (array_filter((array) $jobIds) as $jobId) {
            $this->attachToJob($jobId);
        }
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

            // If old job_id column has value, migrate it to pivot
            if ($expense->job_id) {
                $job = Job::find($expense->job_id);
                if (!$job) {
                    $job = Job::where('job_id', $expense->job_id)->first();
                }

                if ($job && $job->id != $expense->job_id) {
                    // Try to find by job_id field instead
                    $job = Job::where('job_id', $expense->job_id)->first();
                }

                if ($job) {
                    $expense->attachToJob($job->id);
                }
            }
        });
    }
}
