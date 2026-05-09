<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if payment_accounts exists before creating
        if (!Schema::hasTable('payment_accounts')) {
            Schema::create('payment_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('account_name');
                $table->enum('account_type', ['bank', 'cash', 'mobile_banking', 'other']);
                $table->string('account_number')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('branch')->nullable();
                $table->decimal('opening_balance', 15, 2)->default(0);
                $table->decimal('current_balance', 15, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();
            });
        }

        // Check if account_transactions exists before creating
        if (!Schema::hasTable('account_transactions')) {
            Schema::create('account_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payment_account_id')->constrained()->onDelete('cascade');
                $table->string('transaction_type');
                $table->decimal('amount', 15, 2);
                $table->decimal('balance_after', 15, 2);
                $table->string('source_type');
                $table->unsignedBigInteger('source_id')->nullable();
                $table->string('reference_number')->nullable();
                $table->text('description')->nullable();
                $table->date('transaction_date');
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();
            });
        }
    }
};
