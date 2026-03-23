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
 * @property string $property_id
 * @property string $url
 * @property bool $is_main
 * @property int $position порядок отображения в галерее
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Property $property
 */
#[Fillable(
    ['property_id',
        'url',
        'is_main',
        'position',
    ])
]
class PropertyPhoto extends Model
{
    use HasFactory, HasUuids;

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
