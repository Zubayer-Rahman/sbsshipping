<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sbs_jobs', function (Blueprint $table) {
            // First, drop the unique index
            $table->dropUnique('sbs_jobs_job_id_unique');

            // Then, ensure the column is nullable
            $table->string('job_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sbs_jobs', function (Blueprint $table) {
            // Reverse the changes
            $table->string('job_id')->unique()->change();
        });
    }
};
