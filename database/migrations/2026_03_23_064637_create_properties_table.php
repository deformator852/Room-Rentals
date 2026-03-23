<?php

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->enum('property_type', PropertyType::values());
            $table->string('city');
            $table->string('address');
            $table->unsignedTinyInteger('rooms_count');
            $table->float('area');
            $table->decimal('price_per_night', 10, 2);
            $table->unsignedTinyInteger('min_nights')->default(1);
            $table->enum('status', PropertyStatus::values())
                ->default(PropertyStatus::Draft->value);
            $table->float('avg_rating')->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
