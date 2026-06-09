<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('additional_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->foreignId('client_id')->constrained('contacts')->onDelete('cascade');
            $table->foreignId('job_id')->nullable()->constrained('sbs_jobs')->onDelete('cascade');
            $table->string('description');
            $table->decimal('actual_amount', 15, 2)->default(0);
            $table->decimal('to_be_billed', 15, 2)->default(0);
            $table->date('expense_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'billed', 'cancelled'])->default('pending');
            $table->foreignId('billed_to_bill_id')->nullable()->constrained('bills')->onDelete('set null');
            $table->timestamp('billed_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('additional_expenses');
    }
};
