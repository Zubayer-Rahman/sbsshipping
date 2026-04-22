<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id', 'item_name', 'item_code', 'purchase_quantity',
        'unit', 'unit_cost', 'discount_percent', 'unit_cost_before_tax',
        'line_total', 'profit_margin', 'unit_selling_price',
    ];

    protected $casts = [
        'unit_cost'           => 'decimal:2',
        'line_total'          => 'decimal:2',
        'unit_selling_price'  => 'decimal:2',
        'purchase_quantity'   => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}