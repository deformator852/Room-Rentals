<?php

namespace App\Filament\Resources\Properties\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PropertyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),

                TextEntry::make('owner.first_name')
                    ->label('Власник'),

                TextEntry::make('title')
                    ->label('Назва'),

                TextEntry::make('description')
                    ->label('Опис')
                    ->columnSpanFull(),

                TextEntry::make('property_type')
                    ->label('Тип нерухомості')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->label())
                    ->color(fn($state) => $state?->color()),

                TextEntry::make('settlement.name')
                    ->label('Населений пункт'),

                TextEntry::make('address')
                    ->label('Адреса'),

                TextEntry::make('rooms_count')
                    ->label('Кількість кімнат')
                    ->numeric(),

                TextEntry::make('area')
                    ->label('Площа')
                    ->numeric(),

                TextEntry::make('price_per_night')
                    ->label('Ціна за ніч')
                    ->numeric(),

                TextEntry::make('min_nights')
                    ->label('Мінімум ночей')
                    ->numeric(),

                TextEntry::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->label())
                    ->color(fn($state) => $state?->color()),

                TextEntry::make('avg_rating')
                    ->label('Середній рейтинг')
                    ->numeric(),

                TextEntry::make('reviews_count')
                    ->label('Кількість відгуків')
                    ->numeric(),

                TextEntry::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
