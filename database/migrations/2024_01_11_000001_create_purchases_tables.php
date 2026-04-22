<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->nullable()->unique(); // PO2026/0001 auto
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_address')->nullable();
            $table->string('business_location')->default('SBS Shipping (BL0001)');
            $table->dateTime('purchase_date');
            $table->string('pay_term_number')->nullable();
            $table->string('pay_term_type')->nullable(); // Days / Months
            $table->string('document_path')->nullable();
            $table->string('purchase_status')->default('Received'); // Received / Pending / Ordered
            // Totals
            $table->decimal('total_items', 15, 2)->default(0);
            $table->decimal('net_total', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            // Payment
            $table->decimal('payment_amount', 15, 2)->default(0);
            $table->string('payment_status')->default('Due'); // Paid / Due / Partial
            $table->dateTime('paid_on')->nullable();
            $table->string('payment_method')->default('Cash');
            $table->string('payment_account')->nullable();
            $table->text('payment_note')->nullable();
            // Meta
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('added_by')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->string('item_name');
            $table->string('item_code')->nullable();
            $table->decimal('purchase_quantity', 15, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_cost', 15, 2)->default(0);       // before discount
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('unit_cost_before_tax', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->decimal('profit_margin', 5, 2)->default(0);
            $table->decimal('unit_selling_price', 15, 2)->default(0); // inc. tax
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
    }
};
