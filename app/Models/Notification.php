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
 * @property string $user_id
 * @property string $event_type new_booking|booking_confirmed|booking_rejected|new_review|...
 * @property string $message
 * @property array|null $metadata дополнительные данные (например booking_id для формирования ссылки)
 * @property bool $is_read
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 */
#[Fillable(
    ['user_id',
        'event_type',
        'message',
        'metadata',
        'is_read',
    ]
)]
class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean',
    ];

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
