<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name', 'item_code', 'unit',
        'applicable_tax', 'selling_price_tax_type', 'item_type',
        'exc_tax', 'inc_tax', 'margin', 'billing_exc_tax', 'user_id',
    ];

    protected $casts = [
        'exc_tax'         => 'decimal:2',
        'inc_tax'         => 'decimal:2',
        'margin'          => 'decimal:2',
        'billing_exc_tax' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}