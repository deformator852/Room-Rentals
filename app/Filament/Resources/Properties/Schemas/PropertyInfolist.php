<?php

namespace App\Filament\Resources\Properties\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

                TextEntry::make('photos_preview')
                    ->label('Фотографії')
                    ->state(fn ($record) => $record)
                    ->html()
                    ->formatStateUsing(function ($record): string {
                        $photos = $record?->photos?->sortBy('position') ?? collect();

                        if ($photos->isEmpty()) {
                            return '<span class="text-sm text-zinc-500">Немає фото</span>';
                        }

                        $items = $photos->map(function ($photo) {
                            $url = Str::startsWith($photo->url, ['http://', 'https://'])
                                ? $photo->url
                                : Storage::url($photo->url);

                            return '<img src="'.$url.'" alt="Фото обʼєкта" style="width:140px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #e4e4e7;" />';
                        })->implode('');

                        return '<div style="display:flex;flex-wrap:wrap;gap:8px;">'.$items.'</div>';
                    })
                    ->columnSpanFull(),

                TextEntry::make('property_type')
                    ->label('Тип нерухомості')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->label())
                    ->color(fn($state) => $state?->color()),

                TextEntry::make('settlement')
                    ->label('Населений пункт')
                    ->formatStateUsing(function ($state, $record): string {
                        $settlement = $record?->settlement;

                        if (! $settlement) {
                            return '—';
                        }

                        return $settlement->region
                            ? "{$settlement->name}, {$settlement->region}"
                            : $settlement->name;
                    }),

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
