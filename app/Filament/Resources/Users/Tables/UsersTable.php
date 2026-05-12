<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label("Ім'я")
                    ->searchable(['first_name', 'last_name']),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('email_verified_at')
                    ->label('Email підтверджено')
                    ->dateTime()
                    ->placeholder('Ні')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
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
}
