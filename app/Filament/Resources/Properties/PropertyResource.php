<?php

namespace App\Filament\Resources\Properties;

use App\Filament\Resources\Properties\Pages\EditProperty;
use App\Filament\Resources\Properties\Pages\ListProperties;
use App\Filament\Resources\Properties\Pages\ModerationQueue;
use App\Filament\Resources\Properties\Pages\ViewProperty;
use App\Filament\Resources\Properties\Schemas\PropertyForm;
use App\Filament\Resources\Properties\Schemas\PropertyInfolist;
use App\Filament\Resources\Properties\Tables\PropertiesTable;
use App\Models\Property;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;
    protected static ?string $modelLabel = 'об’єкт нерухомості';

    // 2. Название во множественном числе (для заголовка списка и меню)
    protected static ?string $pluralModelLabel = 'нерухомість';

    // 3. Текст в боковом меню (если хотите, чтобы отличался от pluralModelLabel)
    protected static ?string $navigationLabel = 'Нерухомість';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PropertyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PropertyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PropertiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProperties::route('/'),
            'moderation-queue' => ModerationQueue::route('/moderation-queue'),
            'view' => ViewProperty::route('/{record}'),
            'edit' => EditProperty::route('/{record}/edit'),
        ];
    }
}
