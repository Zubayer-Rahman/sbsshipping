<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // If 'payment_account' exists, we'll add the new one
            $table->foreignId('payment_account_id')->nullable()->constrained('payment_accounts')->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['payment_account_id']);
            $table->dropColumn('payment_account_id');
        });
    }
};
