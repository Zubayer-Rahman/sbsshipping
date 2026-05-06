<?php
// app/Models/Contact.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contact_id',
        'type',
        'business_name',
        'name',
        'email',
        'tax_number',
        'pay_term_number',
        'pay_term_type',
        'opening_balance',
        'advance_balance',
        'address',
        'mobile',
        'total_purchase_due',
        'total_purchase_return_due',
        'is_active',
        'user_id',
    ];

    protected $casts = [
        'opening_balance'           => 'decimal:2',
        'advance_balance'           => 'decimal:2',
        'total_purchase_due'        => 'decimal:2',
        'total_purchase_return_due' => 'decimal:2',
        'is_active'                 => 'boolean',
    ];

    public function getPayTermDisplayAttribute(): string
    {
        if ($this->pay_term_number && $this->pay_term_type) {
            return $this->pay_term_number . ' ' . ucfirst($this->pay_term_type);
        }
        return '';
    }

    public function scopeUser($query)
    {
        return $query->whereIn('type', ['user']);
    }

    public function scopeSuppliers($query)
    {
        return $query->whereIn('type', ['supplier', 'both']);
    }

    public function scopeClients($query)
    {
        return $query->whereIn('type', ['client', 'both']);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function generateContactId(): string
    {
        $last = self::withTrashed()
            ->orderByRaw('CAST(SUBSTRING(contact_id, 3) AS UNSIGNED) DESC')
            ->first();

        $next = $last ? ((int) substr($last->contact_id, 2)) + 1 : 1;

        return 'CO' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
