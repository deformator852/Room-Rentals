<?php

namespace App\Enums;

enum PropertyType: string
{
    case Apartment = 'apartment';
    case House = 'house';
    case Room = 'room';
    case Cottage = 'cottage';
    case Studio = 'studio';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Apartment => 'Квартира',
            self::House => 'Будинок',
            self::Room => 'Кімната',
            self::Cottage => 'Котедж',
            self::Studio => 'Студія',
            self::Other => 'Інше',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Apartment => '🏢',
            self::House => '🏠',
            self::Room => '📦',
            self::Cottage => '🌿',
            self::Studio => '🏙️',
            self::Other => '❓',
        };
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = [
                'label' => $case->label(),
                'icon' => $case->icon(),
            ];
        }

        return $options;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
