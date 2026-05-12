<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('cancellation_requested_by_owner_at')->nullable()->after('status');
            $table->timestamp('cancellation_requested_by_tenant_at')->nullable()->after('cancellation_requested_by_owner_at');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending', 'confirmed', 'rejected', 'cancelled', 'check_out') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY status ENUM('pending', 'confirmed', 'rejected', 'cancelled', 'check_out') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'cancellation_requested_by_owner_at',
                'cancellation_requested_by_tenant_at',
            ]);
        });
    }
};
