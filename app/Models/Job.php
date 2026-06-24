<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    // Avoids conflict with Laravel's built-in 'jobs' queue table
    protected $table = 'sbs_jobs';

    protected $fillable = [
        // Original fields
        'job_id',
        'client_name',
        'client_email',
        'client_phone',
        'origin',
        'destination',
        'cargo_type',
        'cargo_weight',
        'cargo_size',
        'pickup_date',
        'eta_date',
        'delivery_date',
        'assigned_agent',
        'cost_amount',
        'expense_amount',
        'is_paid',
        'notes',
        'status',
        'user_id',
        // New fields from Create Job form
        'job_no',
        'awb_no',
        'start_date',
        'receive_date',
        'assigned_user',
        'category',
        'type',
        'items',
        'quantity',
        'cleared_on',
        'vessel_name',
        'invoice_no',
        'invoice_date',
        'rot_no',
        'invoice_value_usd',
        'exchange_rate',
        'imp_exp_value',
        'be_no',
        'be_date',
        'ip_ep_no',
        'ip_ep_date',
        'container_no',
        'shipping_agent',
        'buyer_name',
    ];

    protected $casts = [
        'pickup_date'    => 'date',
        'eta_date'       => 'date',
        'delivery_date'  => 'date',
        'start_date'     => 'date',
        'receive_date'   => 'date',
        'cleared_on'     => 'date',
        'invoice_date'   => 'date',
        'be_date'        => 'date',
        'ip_ep_date'     => 'date',
        'is_paid'        => 'boolean',
        'cost_amount'    => 'decimal:2',
        'expense_amount' => 'decimal:2',
        'type'           => 'string',
        'awb_no'         => 'string',
        'container_no'   => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expenses()
    {
        return $this->belongsToMany(
            Expense::class,
            'expense_job',
            'job_id',
            'expense_id'
        );
    }

    public function expenseJobs()
    {
        return $this->hasMany(ExpenseJob::class, 'job_id');
    }

    public function additionalExpenses()
    {
        return $this->hasMany(AdditionalExpense::class, 'job_id');
    }

    public function additionalExpensesPivot()
    {
        return $this->belongsToMany(
            AdditionalExpense::class,
            'additional_expense_job',
            'job_id',
            'additional_expense_id'
        );
    }

    public function allAdditionalExpenses()
    {
        return AdditionalExpense::where('job_id', $this->id)
            ->orWhereIn('id', function ($query) {
                $query->select('additional_expense_id')
                    ->from('additional_expense_job')
                    ->where('job_id', $this->id);
            });
    }

    public function ious()
    {
        return Iou::whereIn('id', function ($query) {
            $query->select('iou_id')
                ->from('iou_job')
                ->where('job_id', $this->id);
        });
    }

    public function iouJobs()
    {
        return $this->hasMany(IouJob::class, 'job_id');
    }

    public function bills()
    {
        // If one job can have many bills
        return $this->hasMany(Bill::class, 'job_number', 'id'); // Check FK
    }

    public function jobGroups()
    {
        return $this->belongsToMany(
            JobGroup::class,
            'job_group_job',  // pivot table
            'job_id',         // pivot column storing Job's key
            'job_group_id',   // pivot column storing JobGroup's key
            'id',             // ← FIXED: use numeric primary key, not job_id string
            'id'              // JobGroup primary key
        );
    }

    // Auto-generate Job ID before creating
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($job) {
            if (empty($job->job_id)) {
                $job->job_id = "-";
            }
        });
    }
    // Computed: dues = cost - expenses
    public function getDuesAttribute()
    {
        return ($this->cost_amount ?? 0) - ($this->expense_amount ?? 0);
    }

    public function allExpenses()
    {
        // Normal Expenses through pivot
        $normal = $this->expenses()->get()->map(function ($e) {
            return [
                'date' => $e->expense_date,
                'type' => 'Expense',
                'category' => $e->expense_category ?? '—',
                'sub_category' => $e->sub_category ?? '—',
                'note' => $e->expense_note ?? '—',
                'added_by' => $e->added_by ?? '—',
                'amount' => $e->total_amount ?? 0,
            ];
        });

        // ✅ Additional Expenses - direct relationship only
        $additional = $this->additionalExpenses()->get()->map(function ($a) {
            return [
                'date' => $a->expense_date,
                'type' => 'Additional',
                'category' => 'Additional Expense',
                'sub_category' => $a->reference_no ?? '—',
                'note' => $a->description ?? '—',
                'added_by' => optional($a->creator)->name ?? '—',
                'amount' => $a->to_be_billed ?? 0,
            ];
        });

        // IOUs through pivot
        $ious = $this->ious()->get()->map(function ($i) {
            return [
                'date' => $i->created_at,
                'type' => 'IOU',
                'category' => 'IOU - ' . ucfirst($i->type ?? '—'),
                'sub_category' => $i->reference_number ?? '—',
                'note' => $i->description ?? $i->against ?? '—',
                'added_by' => optional($i->creator)->name ?? '—',
                'amount' => $i->amount ?? 0,
            ];
        });

        return $normal->concat($additional)->concat($ious)->sortBy('date')->values();
    }
}
