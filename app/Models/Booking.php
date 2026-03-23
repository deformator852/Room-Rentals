<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $id
 * @property string $property_id
 * @property string $tenant_id
 * @property Carbon $check_in
 * @property Carbon $check_out
 * @property int $nights_count
 * @property string $total_price
 * @property string $status pending|confirmed|rejected|cancelled|completed
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Property      $property
 * @property-read User          $tenant
 * @property-read Review|null   $review
 */
#[Fillable(['property_id',
    'tenant_id',
    'check_in',
    'check_out',
    'nights_count',
    'total_price',
    'status', ])]
class Booking extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
