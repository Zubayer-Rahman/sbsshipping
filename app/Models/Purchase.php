<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'reference_no',
        'supplier_id',
        'supplier_name',
        'supplier_address',
        'business_location',
        'purchase_date',
        'document_path',
        'job_ref_no',
        'purchase_status',
        'total_items',
        'net_total',
        'grand_total',
        'payment_amount',
        'payment_account_id',
        'grand_total',
        'payment_status',
        'paid_on',
        'payment_method',
        'payment_account',
        'payment_note',
        'user_id',
        'added_by',
    ];

    protected $casts = [
        'purchase_date'  => 'datetime',
        'paid_on'        => 'datetime',
        'net_total'      => 'decimal:2',
        'grand_total'    => 'decimal:2',
        'payment_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Contact::class, 'supplier_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($p) {
            if (empty($p->reference_no)) {
                $count = static::count() + 1;
                $p->reference_no = 'PO' . date('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
