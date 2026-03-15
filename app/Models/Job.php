<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id', 'client_name', 'client_email', 'client_phone',
        'origin', 'destination', 'cargo_type', 'cargo_weight',
        'cargo_size', 'pickup_date', 'eta_date', 'delivery_date',
        'assigned_agent', 'cost_amount', 'expense_amount', 'is_paid',
        'notes', 'status', 'user_id',
    ];

    protected $casts = [
        'pickup_date'    => 'date',
        'eta_date'       => 'date',
        'delivery_date'  => 'date',
        'is_paid'        => 'boolean',
        'cost_amount'    => 'decimal:2',
        'expense_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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

    // Computed: dues = cost - paid expenses
    public function getDuesAttribute()
    {
        return ($this->cost_amount ?? 0) - ($this->expense_amount ?? 0);
    }
}