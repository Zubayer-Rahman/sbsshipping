<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iou extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'user_id',
        'job_id',
        'reference_number',
        'amount',
        'type',
        'against',
        'description',
        'status',
        'paid_amount',
        'balance',
        'due_date',
        'paid_date',
        'document',
        'created_by',
        'is_released',
        'expense_id',
        'released_at',
        'released_by',

    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'released_at' => 'datetime',
    ];



    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'iou_job', 'iou_id', 'job_id');
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function releasedBy()
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    public function contact()
    {
        return $this->belongsTo(User::class, 'contact_id');
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(IouPayment::class);
    }

    // Generate unique reference number
    public static function generateReferenceNumber()
    {
        $lastIou = self::latest('id')->first();
        $number = $lastIou ? $lastIou->id + 1 : 1;
        return 'IOU-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    // Update balance after payment
    public function updateBalance()
    {
        $totalPaid = $this->payments()->sum('amount');
        $this->paid_amount = $totalPaid;
        $this->balance = $this->amount - $totalPaid;

        if ($this->balance <= 0) {
            $this->status = 'paid';
            $this->paid_date = now();
        } elseif ($totalPaid > 0) {
            $this->status = 'partial'; // Now correctly sets to partial
        } else {
            $this->status = 'pending';
        }

        $this->save();
    }
}
