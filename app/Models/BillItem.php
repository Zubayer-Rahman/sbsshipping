<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    protected $fillable = [
        'bill_id',
        'item_name',
        'item_code',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'discount',
        'tax',
        'price_inc_tax',
        'subtotal',
    ];
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
