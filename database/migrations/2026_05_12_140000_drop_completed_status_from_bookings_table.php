<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("UPDATE bookings SET status = 'check_out' WHERE status = 'completed'");
        DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending', 'confirmed', 'rejected', 'cancelled', 'check_out') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending', 'confirmed', 'rejected', 'cancelled', 'check_out', 'completed') NOT NULL DEFAULT 'pending'");
    }
};
