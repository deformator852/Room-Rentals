<?php

namespace App\Filament\Resources\Properties\Tables;

use App\Enums\PropertyStatus;
use App\Events\NotificationCreatedEvent;
use App\Models\Notification;
use App\Models\Settlement;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PropertiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Назва')
                    ->searchable(),

                TextColumn::make('property_type')
                    ->label('Тип нерухомості')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label())
                    ->color(fn ($state) => $state?->color()),

                TextColumn::make('settlement.name')
                    ->label('Населений пункт')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label())
                    ->color(fn ($state) => $state?->color()),

                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(
                        collect(PropertyStatus::cases())
                            ->mapWithKeys(fn (PropertyStatus $status) => [$status->value => $status->label()])
                            ->toArray()
                    ),
                SelectFilter::make('property_type')
                    ->label('Тип нерухомості')
                    ->options([
                        'apartment' => 'Квартира',
                        'house' => 'Будинок',
                        'room' => 'Кімната',
                        'cottage' => 'Котедж',
                        'studio' => 'Студія',
                        'other' => 'Інше',
                    ]),
                SelectFilter::make('settlement_id')
                    ->label('Населений пункт')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Settlement::query()
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('region', 'like', "%{$search}%")
                        ->orderBy('name')
                        ->limit(30)
                        ->get()
                        ->mapWithKeys(fn (Settlement $settlement) => [
                            $settlement->id => $settlement->region
                                ? "{$settlement->name}, {$settlement->region}"
                                : $settlement->name,
                        ])
                        ->toArray()
                    )
                    ->getOptionLabelUsing(fn ($value): ?string => Settlement::find($value)?->name
                    ),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Перегляд'),

                EditAction::make()
                    ->label('Редагувати'),
                Action::make('approve')
                    ->label('Опублікувати')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === PropertyStatus::Pending)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            $record->update(['status' => PropertyStatus::Published]);

                            $notification = Notification::query()->create([
                                'user_id' => $record->user_id,
                                'event_type' => 'property_approved',
                                'message' => "Ваше оголошення опубліковано: {$record->title}",
                                'metadata' => [
                                    'property_id' => $record->id,
                                    'action_url' => route('property.show', $record),
                                ],
                                'is_read' => false,
                            ]);

                            broadcast(new NotificationCreatedEvent($notification));

                            $record->moderationLogs()->create([
                                'admin_id' => auth()->id(),
                                'reason' => null,
                                'action' => 'approved',
                            ]);
                        });
                    }),
                Action::make('reject')
                    ->label('Відхилити')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === PropertyStatus::Pending)
                    ->schema([
                        Textarea::make('reason')
                            ->label('Причина відмови')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $record->update(['status' => PropertyStatus::Rejected]);

                            $notification = Notification::query()->create([
                                'user_id' => $record->user_id,
                                'event_type' => 'property_rejected',
                                'message' => "Ваше оголошення відхилено: {$record->title}",
                                'metadata' => [
                                    'property_id' => $record->id,
                                    'reason' => $data['reason'],
                                    'action_url' => route('profile.my-properties'),
                                ],
                                'is_read' => false,
                            ]);

                            broadcast(new NotificationCreatedEvent($notification));

                            $record->moderationLogs()->create([
                                'admin_id' => auth()->id(),
                                'reason' => $data['reason'],
                                'action' => 'rejected',
                            ]);
                        });
                    }),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_approve')
                        ->label('Масово опублікувати')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status !== PropertyStatus::Pending) {
                                    continue;
                                }

                                $record->update(['status' => PropertyStatus::Published]);
                            }
                        }),
                    BulkAction::make('bulk_reject')
                        ->label('Масово відхилити')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->schema([
                            Textarea::make('reason')
                                ->label('Причина відмови')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                if ($record->status !== PropertyStatus::Pending) {
                                    continue;
                                }

                                $record->update(['status' => PropertyStatus::Rejected]);

                                $record->moderationLogs()->create([
                                    'admin_id' => auth()->id(),
                                    'reason' => $data['reason'],
                                    'action' => 'rejected',
                                ]);
                            }
                        }),
                    BulkAction::make('bulk_inactive')
                        ->label('Призупинити')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status !== PropertyStatus::Published) {
                                    continue;
                                }

                                $record->update(['status' => PropertyStatus::Inactive]);
                            }
                        }),
                    DeleteBulkAction::make()
                        ->label('Видалити'),
                ]),
            ]);
    }
}
