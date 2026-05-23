<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iou_payments', function (Blueprint $table) {
            $table->foreignId('job_id')->nullable()->constrained('sbs_jobs')->after('iou_id');
            $table->foreignId('client_id')->nullable()->constrained('contacts')->after('job_id');
        });
    }
};
