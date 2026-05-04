<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ious', function (Blueprint $table) {
            $table->foreignId('job_id')->nullable()->constrained('sbs_jobs')->after('contact_id');
        });
    }

    public function down(): void
    {
        Schema::table('ious', function (Blueprint $table) {
            $table->dropForeign(['job_id']);
            $table->dropColumn('job_id');
        });
    }
};