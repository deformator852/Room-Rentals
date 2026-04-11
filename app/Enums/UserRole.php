<?php

namespace App\Enums;

enum UserRole: string
{
    case Client = 'client';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Client => 'Користувач',
            self::Admin => 'Адміністратор',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
