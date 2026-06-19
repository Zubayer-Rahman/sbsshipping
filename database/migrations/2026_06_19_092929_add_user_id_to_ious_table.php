<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ious', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->after('contact_id');
        });
    }
};
