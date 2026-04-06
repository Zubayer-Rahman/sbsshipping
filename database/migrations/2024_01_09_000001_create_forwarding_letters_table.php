<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forwarding_letters', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no')->nullable();
            $table->date('letter_date')->nullable();
            $table->string('subject')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable(); // FK to contacts
            $table->text('selected_job_ids')->nullable();         // JSON array of sbs_jobs ids
            $table->text('visible_columns')->nullable();          // JSON array of visible col keys
            $table->text('bank_details')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forwarding_letters');
    }
};
