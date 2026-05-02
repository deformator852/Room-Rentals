<?php

namespace App\Filament\Resources\Properties\Schemas;

use App\Enums\PropertyType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PropertyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Select::make('property_type')
                    ->options(PropertyType::class)
                    ->required(),
                TextInput::make('city')
                    ->required(),
                TextInput::make('address')
                    ->required(),
                TextInput::make('rooms_count')
                    ->required()
                    ->numeric(),
                TextInput::make('area')
                    ->required()
                    ->numeric(),
                TextInput::make('price_per_night')
                    ->required()
                    ->numeric(),
                TextInput::make('min_nights')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }
}
