<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'account_type',
        'account_number',
        'bank_name',
        'branch',
        'opening_balance',
        'current_balance',
        'is_active',
        'description',
        'created_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Record a transaction and update balance automatically
     */
    public function recordTransaction($type, $amount, $sourceType, $sourceId, $description, $date, $userId)
    {
        DB::transaction(function () use ($type, $amount, $sourceType, $sourceId, $description, $date, $userId) {
            // Calculate new balance
            if ($type === 'credit') {
                $this->current_balance += $amount;
            } else {
                $this->current_balance -= $amount;
            }

            // Create transaction record
            $this->transactions()->create([
                'transaction_type' => $type,
                'amount' => $amount,
                'balance_after' => $this->current_balance,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'reference_number' => $this->generateReference($sourceType, $sourceId),
                'description' => $description,
                'transaction_date' => $date,
                'created_by' => $userId,
            ]);

            // Save the account balance
            $this->save();
        });
    }

    private function generateReference($sourceType, $sourceId)
    {
        return strtoupper(substr($sourceType, 0, 3)) . '-' . str_pad($sourceId, 6, '0', STR_PAD_LEFT);
    }
}