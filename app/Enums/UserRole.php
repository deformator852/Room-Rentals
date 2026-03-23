<?php

namespace App\Enums;

enum UserRole: string
{
    case Tenant = 'tenant';
    case Landlord = 'landlord';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Tenant => 'Орендар',
            self::Landlord => 'Орендодавець',
            self::Admin => 'Адміністратор',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
