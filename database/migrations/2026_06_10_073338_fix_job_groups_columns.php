<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_groups', function (Blueprint $table) {

            if (!Schema::hasColumn('job_groups', 'name')) {
                $table->string('name')->after('id');
            }

            if (!Schema::hasColumn('job_groups', 'group_code')) {
                $table->string('group_code')->unique()->after('name');
            }

            if (!Schema::hasColumn('job_groups', 'description')) {
                $table->text('description')->nullable()->after('group_code');
            }

            if (!Schema::hasColumn('job_groups', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_groups', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['name', 'group_code', 'description', 'created_by']);
        });
    }
};
