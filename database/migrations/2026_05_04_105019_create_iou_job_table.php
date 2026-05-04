<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iou_job', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iou_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_id')->constrained('sbs_jobs')->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate entries
            $table->unique(['iou_id', 'job_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iou_job');
    }
};