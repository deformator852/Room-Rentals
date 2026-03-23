<?php

namespace App\Enums;

enum PropertyStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Published = 'published';
    case Rejected = 'rejected';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Чернетка',
            self::Pending => 'На модерації',
            self::Published => 'Опубліковано',
            self::Rejected => 'Відхилено',
            self::Inactive => 'Неактивне',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
