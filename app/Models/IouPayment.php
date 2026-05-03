<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IouPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'iou_id',
        'amount',
        'payment_date',
        'payment_method',
        'notes',
        'created_by',
        'job_id',
        'client_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function iou()
    {
        return $this->belongsTo(Iou::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function client()
    {
        return $this->belongsTo(Contact::class, 'client_id');
    }
}