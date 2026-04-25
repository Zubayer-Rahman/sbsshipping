<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ious', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique(); // IOU-001, IOU-002, etc.
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['receivable', 'payable']); // receivable = they owe you, payable = you owe them
            $table->string('against')->nullable(); // What is this IOU for (job, expense, purchase, etc.)
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'partial', 'paid'])->default('pending');
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2);
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->string('document')->nullable(); // Supporting document
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // IOU Payments table for tracking multiple payments
        Schema::create('iou_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iou_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->string('payment_method')->nullable(); // cash, bank transfer, etc.
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iou_payments');
        Schema::dropIfExists('ious');
    }
};