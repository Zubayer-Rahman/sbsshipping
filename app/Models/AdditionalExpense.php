<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'client_id',
        'job_id',
        'description',
        'actual_amount',
        'to_be_billed',
        'expense_date',
        'notes',
        'status',
        'billed_to_bill_id',
        'billed_at',
        'created_by',
        'payment_account_id',
    ];

    protected $casts = [
        'actual_amount' => 'decimal:2',
        'to_be_billed' => 'decimal:2',
        'expense_date' => 'date',
        'billed_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Contact::class, 'client_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function jobs()
    {
        return $this->belongsToMany(
            Job::class,
            'additional_expense_job',
            'additional_expense_id',
            'job_id'
        );
    }

    public function attachToJob($jobId)
    {
        return AdditionalExpenseJob::firstOrCreate([
            'additional_expense_id' => $this->id,
            'job_id' => $jobId,
        ]);
    }

    public function syncJobs(array $jobIds)
    {
        AdditionalExpenseJob::where('additional_expense_id', $this->id)->delete();

        foreach (array_filter($jobIds) as $jobId) {
            $this->attachToJob($jobId);
        }
    }

    public function paymentAccount()
    {
        return $this->belongsTo(PaymentAccount::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'billed_to_bill_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Computed: Profit/Loss
    public function getMarginAttribute()
    {
        return $this->to_be_billed - $this->actual_amount;
    }

    // Generate Reference Number
    public static function generateReferenceNo()
    {
        $last = self::latest('id')->first();
        $number = $last ? $last->id + 1 : 1;
        return 'AE-' . date('Y') . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
