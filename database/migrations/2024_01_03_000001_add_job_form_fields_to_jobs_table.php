<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $cols = Schema::getColumnListing('jobs');

            $add = function($name, $callback) use ($table, $cols) {
                if (!in_array($name, $cols)) $callback($table);
            };

            // Existing fields (from first migration, safe to skip)
            $add('job_id',          fn($t) => $t->string('job_id')->unique()->nullable());
            $add('client_name',     fn($t) => $t->string('client_name')->nullable());
            $add('client_email',    fn($t) => $t->string('client_email')->nullable());
            $add('client_phone',    fn($t) => $t->string('client_phone')->nullable());
            $add('origin',          fn($t) => $t->string('origin')->nullable());
            $add('destination',     fn($t) => $t->string('destination')->nullable());
            $add('cargo_type',      fn($t) => $t->string('cargo_type')->nullable());
            $add('cargo_weight',    fn($t) => $t->decimal('cargo_weight', 10, 2)->nullable());
            $add('cargo_size',      fn($t) => $t->string('cargo_size')->nullable());
            $add('pickup_date',     fn($t) => $t->date('pickup_date')->nullable());
            $add('eta_date',        fn($t) => $t->date('eta_date')->nullable());
            $add('delivery_date',   fn($t) => $t->date('delivery_date')->nullable());
            $add('assigned_agent',  fn($t) => $t->string('assigned_agent')->nullable());
            $add('cost_amount',     fn($t) => $t->decimal('cost_amount', 15, 2)->nullable());
            $add('expense_amount',  fn($t) => $t->decimal('expense_amount', 15, 2)->nullable());
            $add('is_paid',         fn($t) => $t->boolean('is_paid')->default(false));
            $add('notes',           fn($t) => $t->text('notes')->nullable());
            $add('status',          fn($t) => $t->string('status')->default('Not Started'));
            $add('user_id',         fn($t) => $t->unsignedBigInteger('user_id')->nullable());

            // NEW fields from Create Job form (screenshot)
            $add('job_no',              fn($t) => $t->string('job_no')->nullable());
            $add('awb_no',              fn($t) => $t->string('awb_no')->nullable());
            $add('start_date',          fn($t) => $t->date('start_date')->nullable());
            $add('receive_date',        fn($t) => $t->date('receive_date')->nullable());
            $add('assigned_user',       fn($t) => $t->string('assigned_user')->nullable());
            $add('category',            fn($t) => $t->string('category')->nullable());
            $add('items',               fn($t) => $t->string('items')->nullable());
            $add('quantity',            fn($t) => $t->integer('quantity')->nullable());
            $add('cleared_on',          fn($t) => $t->date('cleared_on')->nullable());
            $add('vessel_name',         fn($t) => $t->string('vessel_name')->nullable());
            $add('invoice_no',          fn($t) => $t->string('invoice_no')->nullable());
            $add('invoice_date',        fn($t) => $t->date('invoice_date')->nullable());
            $add('rot_no',              fn($t) => $t->string('rot_no')->nullable());
            $add('invoice_value_usd',   fn($t) => $t->decimal('invoice_value_usd', 15, 2)->nullable());
            $add('exchange_rate',       fn($t) => $t->decimal('exchange_rate', 12, 4)->nullable());
            $add('imp_exp_value',       fn($t) => $t->decimal('imp_exp_value', 15, 2)->nullable());
            $add('be_no',               fn($t) => $t->string('be_no')->nullable());
            $add('be_date',             fn($t) => $t->date('be_date')->nullable());
            $add('ip_ep_no',            fn($t) => $t->string('ip_ep_no')->nullable());
            $add('ip_ep_date',          fn($t) => $t->date('ip_ep_date')->nullable());
            $add('container_no',        fn($t) => $t->string('container_no')->nullable());
            $add('shipping_agent',      fn($t) => $t->string('shipping_agent')->nullable());
            $add('buyer_name',          fn($t) => $t->string('buyer_name')->nullable());
        });
    }

    public function down(): void
    {
        // Drop only the NEW columns added in this migration
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn([
                'job_no', 'awb_no', 'start_date', 'receive_date',
                'assigned_user', 'category', 'items', 'quantity',
                'cleared_on', 'vessel_name', 'invoice_no', 'invoice_date',
                'rot_no', 'invoice_value_usd', 'exchange_rate', 'imp_exp_value',
                'be_no', 'be_date', 'ip_ep_no', 'ip_ep_date',
                'container_no', 'shipping_agent', 'buyer_name',
            ]);
        });
    }
};
