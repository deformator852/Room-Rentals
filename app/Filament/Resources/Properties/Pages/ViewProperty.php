<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Enums\PropertyStatus;
use App\Filament\Resources\Properties\PropertyResource;
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
