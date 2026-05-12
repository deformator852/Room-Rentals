<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Enums\PropertyStatus;
use App\Filament\Resources\Properties\PropertyResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ModerationQueue extends ListRecords
{
    protected static string $resource = PropertyResource::class;

    protected static ?string $title = 'Черга модерації';

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', PropertyStatus::Pending);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}

