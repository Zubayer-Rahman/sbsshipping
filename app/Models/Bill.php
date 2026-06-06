<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'bill_no',
        'business_location',
        'client_id',
        'client_name',
        'client_contact',
        'billing_address',
        'shipping_address',
        'pay_term_number',
        'pay_term_type',
        'billing_date',
        'status',
        'job_number',
        'shipping_status',
        'discount_type',
        'discount_amount',
        'discount_value',
        'order_tax',
        'order_tax_value',
        'total_items',
        'sub_total',
        'shipping_charges',
        'total_payable',
        'total_paid',
        'total_remaining',
        'payment_status',
        'payment_method',
        'payment_account',
        'payment_note',
        'paid_on',
        'billing_note',
        'staff_note',
        'user_id',
        'added_by',
    ];

    protected $casts = [
        'billing_date'  => 'datetime',
        'paid_on'       => 'datetime',
        'total_payable' => 'decimal:2',
        'total_paid'    => 'decimal:2',
        'total_remaining' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }
    public function payments()
    {
        return $this->hasMany(BillPayment::class);
    }
    public function client()
    {
        return $this->belongsTo(Contact::class, 'client_id');
    }
    public function additionalExpenses()
    {
        return $this->hasMany(BillAdditionalExpense::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($b) {
            if (empty($b->bill_no)) {
                $max = static::max('bill_no') ?? 0;
                $b->bill_no = intval($max) + 1;
            }
        });
    }
}
