<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Enums\PropertyStatus;
use App\Events\NotificationCreatedEvent;
use App\Filament\Resources\Properties\PropertyResource;
use App\Models\Notification;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class ViewProperty extends ViewRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('approve')
                ->label('Дозволити публікацію')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->visible(fn () => $this->record->isPending())
                ->requiresConfirmation()
                ->modalHeading('Опублікувати оголошення')
                ->modalDescription('Ви впевнені, що хочете дозволити публікацію цього оголошення?')
                ->action(function () {
                    DB::transaction(function () {
                        $this->record->update([
                            'status' => PropertyStatus::Published,
                        ]);

                        $notification = Notification::query()->create([
                            'user_id' => $this->record->user_id,
                            'event_type' => 'property_approved',
                            'message' => "Ваше оголошення опубліковано: {$this->record->title}",
                            'metadata' => [
                                'property_id' => $this->record->id,
                                'action_url' => route('property.show', $this->record),
                            ],
                            'is_read' => false,
                        ]);
                        broadcast(new NotificationCreatedEvent($notification));

                        $this->record->moderationLogs()->create([
                            'admin_id' => auth()->id(),
                            'reason' => null,
                            'action' => 'approved',
                        ]);
                    });
                }),
            Action::make('reject')
                ->label('Відмовити у публікації')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn () => $this->record->isPending())
                ->requiresConfirmation()
                ->modalHeading('Відхилити оголошення')
                ->schema([
                    Textarea::make('reason')
                        ->label('Причина відмови')
                        ->placeholder('Опишіть причину...')
                        ->required(),
                ])
                ->action(function (array $data) {
                    DB::transaction(function () use ($data) {
                        $this->record->update([
                            'status' => PropertyStatus::Rejected,
                        ]);

                        $notification = Notification::query()->create([
                            'user_id' => $this->record->user_id,
                            'event_type' => 'property_rejected',
                            'message' => "Ваше оголошення відхилено: {$this->record->title}",
                            'metadata' => [
                                'property_id' => $this->record->id,
                                'reason' => $data['reason'],
                                'action_url' => route('profile.my-properties'),
                            ],
                            'is_read' => false,
                        ]);
                        broadcast(new NotificationCreatedEvent($notification));

                        $this->record->moderationLogs()->create([
                            'admin_id' => auth()->id(),
                            'reason' => $data['reason'],
                            'action' => 'rejected',
                        ]);
                    });

                    //                    Notification::make()
                    //                        ->title('Оголошення відхилено')
                    //                        ->danger()
                    //                        ->send();
                }),
        ];
    }
}
