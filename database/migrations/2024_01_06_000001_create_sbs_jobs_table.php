<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create fresh sbs_jobs table — completely separate from Laravel's queue table
        if (!Schema::hasTable('sbs_jobs')) {
            Schema::create('sbs_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('job_id')->unique()->nullable();
                $table->string('job_no')->nullable();
                $table->string('awb_no')->nullable();
                $table->date('start_date')->nullable();
                $table->date('receive_date')->nullable();
                $table->string('client_name')->nullable();
                $table->string('client_email')->nullable();
                $table->string('client_phone')->nullable();
                $table->unsignedBigInteger('assigned_user')->nullable();
                $table->string('assigned_agent')->nullable();
                $table->string('category')->nullable();
                $table->string('items')->nullable();
                $table->integer('quantity')->nullable();
                $table->string('origin')->nullable();
                $table->string('destination')->nullable();
                $table->date('cleared_on')->nullable();
                $table->string('vessel_name')->nullable();
                $table->string('invoice_no')->nullable();
                $table->date('invoice_date')->nullable();
                $table->string('rot_no')->nullable();
                $table->decimal('invoice_value_usd', 15, 2)->nullable();
                $table->decimal('exchange_rate', 12, 4)->nullable();
                $table->decimal('imp_exp_value', 15, 2)->nullable();
                $table->string('be_no')->nullable();
                $table->date('be_date')->nullable();
                $table->string('ip_ep_no')->nullable();
                $table->date('ip_ep_date')->nullable();
                $table->string('container_no')->nullable();
                $table->string('shipping_agent')->nullable();
                $table->string('buyer_name')->nullable();
                $table->string('cargo_type')->nullable();
                $table->decimal('cargo_weight', 10, 2)->nullable();
                $table->string('cargo_size')->nullable();
                $table->date('pickup_date')->nullable();
                $table->date('eta_date')->nullable();
                $table->date('delivery_date')->nullable();
                $table->decimal('cost_amount', 15, 2)->nullable();
                $table->decimal('expense_amount', 15, 2)->nullable();
                $table->boolean('is_paid')->default(false);
                $table->text('notes')->nullable();
                $table->string('status')->default('Not Started');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sbs_jobs');
    }
};
