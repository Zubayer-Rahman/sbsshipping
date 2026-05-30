<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillPayment extends Model
{
    protected $fillable = [
        'bill_id',
        'reference_no',
        'amount',
        'payment_method',
        'payment_note',
        'paid_on',
        'user_id',
    ];
    protected $casts = ['paid_on' => 'datetime'];
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
