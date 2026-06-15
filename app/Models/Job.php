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
        'awb_no'         => 'float:20',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groups()
    {
        return $this->belongsToMany(JobGroup::class, 'job_group_job', 'job_id', 'job_group_id');
    }

    // Auto-generate Job ID before creating
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($job) {
            if (empty($job->job_id)) {
                $count = static::count() + 1;
                $job->job_id = 'SBS-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Computed: dues = cost - expenses
    public function getDuesAttribute()
    {
        return ($this->cost_amount ?? 0) - ($this->expense_amount ?? 0);
    }
}
