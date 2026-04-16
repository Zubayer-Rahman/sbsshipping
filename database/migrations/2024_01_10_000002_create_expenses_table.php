<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_ref')->nullable();           // EP2026/4971 - auto generated
            $table->string('business_location')->default('SBS Shipping (BL0001)');
            $table->string('expense_category')->nullable();      // Job Expense / Office Expense
            $table->string('sub_category')->nullable();          // from expense_categories
            $table->unsignedBigInteger('job_id')->nullable();    // linked sbs_jobs.id
            $table->string('job_ref_no')->nullable();            // job_no for display
            $table->dateTime('expense_date');
            $table->string('expense_for')->nullable();           // user name
            $table->string('expense_for_contact')->nullable();   // contact name
            $table->string('applicable_tax')->default('None');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->boolean('is_refund')->default(false);
            $table->string('document_path')->nullable();
            $table->text('expense_note')->nullable();
            // Recurring
            $table->boolean('is_recurring')->default(false);
            $table->integer('recurring_interval')->nullable();
            $table->string('recurring_interval_type')->default('Days'); // Days/Months/Years
            $table->integer('no_of_repetitions')->nullable();
            // Payment
            $table->decimal('payment_amount', 15, 2)->default(0);
            $table->dateTime('paid_on')->nullable();
            $table->string('payment_method')->default('Cash');
            $table->string('payment_account')->nullable();
            $table->text('payment_note')->nullable();
            $table->string('payment_status')->default('Due'); // Paid / Due / Partial
            $table->decimal('payment_due', 15, 2)->default(0);
            // Meta
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('added_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
