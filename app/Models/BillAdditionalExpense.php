<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillAdditionalExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'description',
        'amount',
        'job_id',
        'is_auto',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_auto' => 'boolean',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
}
