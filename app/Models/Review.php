<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $booking_id
 * @property string $tenant_id
 * @property string $property_id
 * @property int $rating 1–5
 * @property string $comment
 * @property string|null $owner_reply
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Booking  $booking
 * @property-read User     $tenant
 * @property-read Property $property
 */
#[Fillable(['booking_id',
    'tenant_id',
    'property_id',
    'rating',
    'comment',
    'owner_reply',
])]
class Review extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'rating' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
