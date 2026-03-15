<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            // Only add columns if they don't already exist
            if (!Schema::hasColumn('jobs', 'job_id')) {
                $table->string('job_id')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('jobs', 'client_name')) {
                $table->string('client_name')->nullable();
            }
            if (!Schema::hasColumn('jobs', 'client_email')) {
                $table->string('client_email')->nullable();
            }
            if (!Schema::hasColumn('jobs', 'client_phone')) {
                $table->string('client_phone')->nullable();
            }
            if (!Schema::hasColumn('jobs', 'origin')) {
                $table->string('origin')->nullable();
            }
            if (!Schema::hasColumn('jobs', 'destination')) {
                $table->string('destination')->nullable();
            }
            if (!Schema::hasColumn('jobs', 'cargo_type')) {
                $table->string('cargo_type')->nullable();
            }
            if (!Schema::hasColumn('jobs', 'cargo_weight')) {
                $table->decimal('cargo_weight', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('jobs', 'cargo_size')) {
                $table->string('cargo_size')->nullable();
            }
            if (!Schema::hasColumn('jobs', 'pickup_date')) {
                $table->date('pickup_date')->nullable();
            }
            if (!Schema::hasColumn('jobs', 'eta_date')) {
                $table->date('eta_date')->nullable();
            }
            if (!Schema::hasColumn('jobs', 'delivery_date')) {
                $table->date('delivery_date')->nullable();
            }
            if (!Schema::hasColumn('jobs', 'assigned_agent')) {
                $table->string('assigned_agent')->nullable();
            }
            if (!Schema::hasColumn('jobs', 'cost_amount')) {
                $table->decimal('cost_amount', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('jobs', 'expense_amount')) {
                $table->decimal('expense_amount', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('jobs', 'is_paid')) {
                $table->boolean('is_paid')->default(false);
            }
            if (!Schema::hasColumn('jobs', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('jobs', 'status')) {
                $table->string('status')->default('pending');
            }
            if (!Schema::hasColumn('jobs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn([
                'job_id', 'client_name', 'client_email', 'client_phone',
                'origin', 'destination', 'cargo_type', 'cargo_weight',
                'cargo_size', 'pickup_date', 'eta_date', 'delivery_date',
                'assigned_agent', 'cost_amount', 'expense_amount',
                'is_paid', 'notes', 'status', 'user_id',
            ]);
        });
    }
};
