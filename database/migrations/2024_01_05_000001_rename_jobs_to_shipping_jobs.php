<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Only rename if 'jobs' exists and 'shipping_jobs' does not
        if (Schema::hasTable('jobs') && !Schema::hasTable('shipping_jobs')) {
            Schema::rename('jobs', 'shipping_jobs');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('shipping_jobs') && !Schema::hasTable('jobs')) {
            Schema::rename('shipping_jobs', 'jobs');
        }
    }
};
