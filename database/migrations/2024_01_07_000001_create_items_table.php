<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('item_code')->nullable();
            $table->string('unit')->default('Nos (Nos)');
            $table->string('applicable_tax')->default('None');
            $table->string('selling_price_tax_type')->default('Exclusive');
            $table->string('item_type')->default('Single');
            $table->decimal('exc_tax', 15, 2)->nullable();       // Default Purchase Price excl. tax
            $table->decimal('inc_tax', 15, 2)->nullable();       // Default Purchase Price incl. tax
            $table->decimal('margin', 10, 2)->nullable();        // x Margin(%)
            $table->decimal('billing_exc_tax', 15, 2)->nullable(); // Billing Amount excl. tax
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
