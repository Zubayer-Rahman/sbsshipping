<?php
// database/migrations/2025_04_01_000001_create_contacts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('contact_id')->unique();
            $table->enum('type', ['supplier', 'client', 'both'])->default('supplier');
            $table->string('business_name')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('tax_number')->nullable();
            $table->integer('pay_term_number')->nullable();
            $table->enum('pay_term_type', ['days', 'months'])->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0.00);
            $table->decimal('advance_balance', 15, 2)->default(0.00);
            $table->text('address')->nullable();
            $table->string('mobile')->nullable();
            $table->decimal('total_purchase_due', 15, 2)->default(0.00);
            $table->decimal('total_purchase_return_due', 15, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};