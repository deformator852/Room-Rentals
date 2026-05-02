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
                TextEntry::make('title'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('property_type')
                    ->badge(),
                TextEntry::make('city'),
                TextEntry::make('address'),
                TextEntry::make('rooms_count')
                    ->numeric(),
                TextEntry::make('area')
                    ->numeric(),
                TextEntry::make('price_per_night')
                    ->numeric(),
                TextEntry::make('min_nights')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('avg_rating')
                    ->numeric(),
                TextEntry::make('reviews_count')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
