<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'region',
        'district',
        'community',
        'katottg_code',
        'lat',
        'lon',
    ];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function fullName(): string
    {
        return trim(collect([$this->type, $this->name, $this->region])->filter()->implode(' '));
    }
}
