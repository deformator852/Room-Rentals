<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')->label('ID'),
                TextEntry::make('name')->label("Ім'я та прізвище"),
                TextEntry::make('email')->label('Email'),
                TextEntry::make('email_verified_at')->label('Email підтверджено')->dateTime()->placeholder('Ні'),
                TextEntry::make('created_at')->label('Створено')->dateTime(),
                TextEntry::make('updated_at')->label('Оновлено')->dateTime(),
            ]);
    }
}
