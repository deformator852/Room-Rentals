<?php

namespace App\Models;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $user_id
 * @property string $title
 * @property string $description
 * @property PropertyType $property_type
 * @property string $city
 * @property string $address
 * @property int $rooms_count
 * @property float $area
 * @property numeric $price_per_night
 * @property int $min_nights
 * @property PropertyStatus $status
 * @property float $avg_rating
 * @property-read int|null $reviews_count
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read Collection<int, Favorite> $favorites
 * @property-read int|null $favorites_count
 * @property-read Collection<int, PropertyPhoto> $mainPhoto
 * @property-read int|null $main_photo_count
 * @property-read Collection<int, ModerationLog> $moderationLogs
 * @property-read int|null $moderation_logs_count
 * @property-read User $owner
 * @property-read Collection<int, PropertyPhoto> $photos
 * @property-read int|null $photos_count
 * @property-read Collection<int, Review> $reviews
 */
class Property extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'property_type',
        'city',
        'address',
        'rooms_count',
        'area',
        'price_per_night',
        'min_nights',
        'status',
        'avg_rating',
        'reviews_count',
    ];

    protected $casts = [
        'property_type' => PropertyType::class,
        'status' => PropertyStatus::class,
        'price_per_night' => 'decimal:2',
        'avg_rating' => 'float',
        'area' => 'float',
    ];

    // --- Status helpers ---

    public function isPublished(): bool
    {
        return $this->status === PropertyStatus::Published;
    }

    public function isPending(): bool
    {
        return $this->status === PropertyStatus::Pending;
    }

    // --- Relations ---

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class);
    }

    public function mainPhoto(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class)->where('is_main', true);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function moderationLogs(): HasMany
    {
        return $this->hasMany(ModerationLog::class);
    }
}
