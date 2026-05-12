<?php

namespace App\Filament\Resources\Bookings\Schemas;

use App\Models\Booking;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        $isLocked = fn(?Booking $record): bool => in_array($record?->status, ['cancelled', 'check_out', 'rejected'], true);

        return $schema
            ->components([
                Select::make('property_id')
                    ->label('Об’єкт')
                    ->relationship('property', 'title')
                    ->searchable()
                    ->disabled($isLocked)
                    ->required(),

                Select::make('tenant_id')
                    ->label('Орендар')
                    ->relationship('tenant', 'email')
                    ->searchable()
                    ->disabled($isLocked)
                    ->required(),

                DatePicker::make('check_in')
                    ->label('Дата заїзду')
                    ->disabled($isLocked)
                    ->required(),

                DatePicker::make('check_out')
                    ->label('Дата виїзду')
                    ->disabled($isLocked)
                    ->required(),

                TextInput::make('nights_count')
                    ->label('Кількість ночей')
                    ->numeric()
                    ->disabled($isLocked)
                    ->required(),

                TextInput::make('total_price')
                    ->label('Сума')
                    ->numeric()
                    ->disabled($isLocked)
                    ->required(),

                Select::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Очікує',
                        'confirmed' => 'Підтверджено',
                        'rejected' => 'Відхилено',
                        'cancelled' => 'Скасовано',
                        'check_out' => 'Завершено',
                    ])
                    ->disabled($isLocked)
                    ->required(),

                Textarea::make('comment')
                    ->label('Коментар')
                    ->disabled($isLocked)
                    ->columnSpanFull(),
            ]);
    }
}
