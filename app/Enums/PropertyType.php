<?php

namespace App\Enums;

enum PropertyType: string
{
    case Apartment = 'apartment';
    case House = 'house';
    case Room = 'room';
    case Cottage = 'cottage';

    public function label(): string
    {
        return match ($this) {
            self::Apartment => 'Квартира',
            self::House => 'Будинок',
            self::Room => 'Кімната',
            self::Cottage => 'Котедж',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
