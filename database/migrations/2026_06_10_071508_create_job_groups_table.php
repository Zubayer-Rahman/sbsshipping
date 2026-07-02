<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Job Groups table
        Schema::create('job_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('group_code')->unique(); // Auto-generated like GRP-2026-0001
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'completed', 'archived'])->default('active');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // Pivot table: Many-to-Many between jobs and job_groups
        Schema::create('job_group_job', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_group_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_id')->constrained('sbs_jobs')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['job_group_id', 'job_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_group_job');
        Schema::dropIfExists('job_groups');
    }
};
