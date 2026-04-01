<?php
// database/migrations/2025_04_01_000002_fix_contacts_name_column.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('contacts', 'first_name')) {
                $table->dropColumn('first_name');
            }
            if (Schema::hasColumn('contacts', 'last_name')) {
                $table->dropColumn('last_name');
            }

            // Add single 'name' column if it doesn't exist
            if (!Schema::hasColumn('contacts', 'name')) {
                $table->string('name')->nullable()->after('business_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('first_name')->nullable()->after('business_name');
            $table->string('last_name')->nullable()->after('first_name');
        });
    }
};