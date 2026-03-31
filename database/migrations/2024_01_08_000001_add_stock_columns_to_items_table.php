<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $cols = Schema::getColumnListing('items');

            if (!in_array('current_stock', $cols))
                $table->decimal('current_stock', 15, 2)->default(0);
            if (!in_array('category', $cols))
                $table->string('category')->nullable();
            if (!in_array('brand', $cols))
                $table->string('brand')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['current_stock', 'category', 'brand']);
        });
    }
};
