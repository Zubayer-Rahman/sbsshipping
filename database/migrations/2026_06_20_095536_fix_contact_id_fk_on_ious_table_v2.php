<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE ious DROP FOREIGN KEY ious_contact_id_foreign');
        DB::statement('ALTER TABLE ious ADD CONSTRAINT ious_contact_id_foreign FOREIGN KEY (contact_id) REFERENCES users(id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE ious DROP FOREIGN KEY ious_contact_id_foreign');
        DB::statement('ALTER TABLE ious ADD CONSTRAINT ious_contact_id_foreign FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE');
    }
};