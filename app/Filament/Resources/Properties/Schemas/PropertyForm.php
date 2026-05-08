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
                    ->label('Назва')
                    ->required(),

                Textarea::make('description')
                    ->label('Опис')
                    ->required()
                    ->columnSpanFull(),

                Select::make('property_type')
                    ->label('Тип нерухомості')
                    ->options(PropertyType::class)
                    ->required(),

                Select::make('settlement_id')
                    ->label('Населений пункт')
                    ->relationship('settlement', 'name')
                    ->searchable()
                    ->required(),

                TextInput::make('address')
                    ->label('Адреса')
                    ->required(),

                TextInput::make('rooms_count')
                    ->label('Кількість кімнат')
                    ->required()
                    ->numeric(),

                TextInput::make('area')
                    ->label('Площа (м²)')
                    ->required()
                    ->numeric(),

                TextInput::make('price_per_night')
                    ->label('Ціна за ніч')
                    ->required()
                    ->numeric(),

                TextInput::make('min_nights')
                    ->label('Мінімальна кількість ночей')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }
}
