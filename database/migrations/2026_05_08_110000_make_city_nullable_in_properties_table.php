<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE properties MODIFY city VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement("UPDATE properties SET city = '' WHERE city IS NULL");
        DB::statement('ALTER TABLE properties MODIFY city VARCHAR(255) NOT NULL');
    }
};
