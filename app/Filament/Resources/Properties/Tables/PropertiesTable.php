<?php

namespace App\Filament\Resources\Properties\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PropertiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Назва')
                    ->searchable(),

                TextColumn::make('property_type')
                    ->label('Тип нерухомості')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->label())
                    ->color(fn($state) => $state?->color()),

                TextColumn::make('settlement.name')
                    ->label('Населений пункт')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->label())
                    ->color(fn($state) => $state?->color()),

                TextColumn::make('avg_rating')
                    ->label('Середній рейтинг')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('reviews_count')
                    ->label('Кількість відгуків')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Перегляд'),

                EditAction::make()
                    ->label('Редагувати'),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Видалити'),
                ]),
            ]);
    }
}
