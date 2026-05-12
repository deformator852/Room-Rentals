<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')->label('ID'),
                TextEntry::make('property.title')->label('Об’єкт'),
                TextEntry::make('tenant.name')->label('Орендар'),
                TextEntry::make('check_in')->label('Дата заїзду')->date(),
                TextEntry::make('check_out')->label('Дата виїзду')->date(),
                TextEntry::make('nights_count')->label('Ночей')->numeric(),
                TextEntry::make('total_price')->label('Сума')->numeric(),
                TextEntry::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => self::statusLabel($state))
                    ->color(fn (?string $state) => self::statusColor($state)),
                TextEntry::make('comment')->label('Коментар')->placeholder('-')->columnSpanFull(),
                TextEntry::make('created_at')->label('Створено')->dateTime(),
                TextEntry::make('updated_at')->label('Оновлено')->dateTime(),
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
