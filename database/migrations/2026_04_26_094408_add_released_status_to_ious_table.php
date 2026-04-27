<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ious', function (Blueprint $table) {
            $table->boolean('is_released')->default(false)->after('status');
            $table->foreignId('expense_id')->nullable()->constrained()->after('is_released');
            $table->timestamp('released_at')->nullable()->after('expense_id');
            $table->foreignId('released_by')->nullable()->constrained('users')->after('released_at');
        });
    }

    public function down(): void
    {
        Schema::table('ious', function (Blueprint $table) {
            $table->dropForeign(['expense_id']);
            $table->dropForeign(['released_by']);
            $table->dropColumn(['is_released', 'expense_id', 'released_at', 'released_by']);
        });
    }
};