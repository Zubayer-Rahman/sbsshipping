<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('job_group_job')) {
            Schema::create('job_group_job', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_group_id')->constrained('job_groups')->onDelete('cascade');
                $table->foreignId('job_id')->constrained('sbs_jobs')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['job_group_id', 'job_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_group_job');
    }
};
