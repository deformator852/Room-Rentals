<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.title')
                    ->label('Об’єкт')
                    ->searchable(),

                TextColumn::make('tenant.name')
                    ->label('Орендар')
                    ->searchable(),

                TextColumn::make('check_in')
                    ->label('Заїзд')
                    ->date()
                    ->sortable(),

                TextColumn::make('check_out')
                    ->label('Виїзд')
                    ->date()
                    ->sortable(),

                TextColumn::make('total_price')
                    ->label('Сума')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => self::statusLabel($state))
                    ->color(fn (?string $state) => self::statusColor($state))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Очікує',
                        'confirmed' => 'Підтверджено',
                        'rejected' => 'Відхилено',
                        'cancelled' => 'Скасовано',
                        'check_out' => 'Завершено',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()->label('Перегляд'),
                EditAction::make()->label('Редагувати'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Видалити'),
                ]),
            ]);
    }

    private static function statusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Очікує',
            'confirmed' => 'Підтверджено',
            'rejected' => 'Відхилено',
            'cancelled' => 'Скасовано',
            'check_out' => 'Завершено',
            default => (string) $status,
        };
    }

    private static function statusColor(?string $status): string
    {
        return match ($status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'gray',
            'check_out' => 'info',
            default => 'gray',
        };
    }
}
