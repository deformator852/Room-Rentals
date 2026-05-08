<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('settlement_id')->nullable()->after('property_type')->constrained('settlements')->nullOnDelete();
        });

        $cities = DB::table('properties')
            ->select('city')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->pluck('city');

        foreach ($cities as $city) {
            $settlementId = DB::table('settlements')->insertGetId([
                'name' => $city,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('properties')
                ->where('city', $city)
                ->update(['settlement_id' => $settlementId]);
        }
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropConstrainedForeignId('settlement_id');
        });
    }
};
