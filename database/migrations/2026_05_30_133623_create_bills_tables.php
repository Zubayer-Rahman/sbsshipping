<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no')->nullable()->unique();      // auto: 2486
            $table->string('business_location')->default('SBS Shipping (BL0001)');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('client_name')->nullable();
            $table->string('client_contact')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('pay_term_number')->nullable();
            $table->string('pay_term_type')->nullable();
            $table->dateTime('billing_date');
            $table->string('status')->default('Final');           // Final / Draft
            $table->string('job_number')->nullable();
            $table->string('shipping_status')->nullable();
            // Discount
            $table->string('discount_type')->default('Percentage'); // Percentage / Fixed
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('discount_value', 15, 2)->default(0);  // computed
            // Tax
            $table->string('order_tax')->default('None');
            $table->decimal('order_tax_value', 15, 2)->default(0);
            // Totals
            $table->decimal('total_items', 15, 2)->default(0);
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('shipping_charges', 15, 2)->default(0);
            $table->decimal('total_payable', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('total_remaining', 15, 2)->default(0);
            // Payment
            $table->string('payment_status')->default('Due');
            $table->string('payment_method')->nullable();
            $table->string('payment_account')->nullable();
            $table->text('payment_note')->nullable();
            $table->dateTime('paid_on')->nullable();
            // Notes
            $table->text('billing_note')->nullable();
            $table->text('staff_note')->nullable();
            // Meta
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('added_by')->nullable();
            $table->timestamps();
        });

        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->string('item_name');
            $table->string('item_code')->nullable();
            $table->text('description')->nullable();
            $table->decimal('quantity', 15, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('price_inc_tax', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('bill_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->string('reference_no')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('payment_method')->default('Cash');
            $table->text('payment_note')->nullable();
            $table->dateTime('paid_on');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_payments');
        Schema::dropIfExists('bill_items');
        Schema::dropIfExists('bills');
    }
};
