<?php

namespace App\Filament\Resources\Properties\Schemas;

use App\Enums\PropertyType;
use App\Enums\PropertyStatus;
use App\Models\Settlement;
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
                    ->options(
                        collect(PropertyType::cases())
                            ->mapWithKeys(fn (PropertyType $type) => [$type->value => $type->label()])
                            ->toArray()
                    )
                    ->required(),

                Select::make('settlement_id')
                    ->label('Населений пункт')
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search): array {
                        return Settlement::query()
                            ->where(function ($query) use ($search) {
                                $query
                                    ->where('name', 'like', "%{$search}%")
                                    ->orWhere('region', 'like', "%{$search}%");
                            })
                            ->orderBy('name')
                            ->limit(30)
                            ->get()
                            ->mapWithKeys(fn (Settlement $settlement) => [
                                $settlement->id => $settlement->region
                                    ? "{$settlement->name}, {$settlement->region}"
                                    : $settlement->name,
                            ])
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function ($value): ?string {
                        if (! $value) {
                            return null;
                        }

                        $settlement = Settlement::query()->find($value);

                        if (! $settlement) {
                            return null;
                        }

                        return $settlement->region
                            ? "{$settlement->name}, {$settlement->region}"
                            : $settlement->name;
                    })
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

                Select::make('status')
                    ->label('Статус')
                    ->options(
                        collect(PropertyStatus::cases())
                            ->mapWithKeys(fn (PropertyStatus $status) => [$status->value => $status->label()])
                            ->toArray()
                    )
                    ->required(),
            ]);
    }
}
