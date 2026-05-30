<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // We remove ->after('amount') to avoid the "column not found" error
            if (!Schema::hasColumn('expenses', 'payment_account_id')) {
                $table->foreignId('payment_account_id')->nullable()
                    ->constrained('payment_accounts')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['payment_account_id']);
            $table->dropColumn('payment_account_id');
        });
    }
};
