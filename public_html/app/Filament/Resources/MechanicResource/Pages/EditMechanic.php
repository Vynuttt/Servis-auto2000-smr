<?php

namespace App\Filament\Resources\MechanicResource\Pages;

use App\Filament\Resources\MechanicResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditMechanic extends EditRecord
{
    protected static string $resource = MechanicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Mechanic Updated')
            ->body("Mechanic {$this->record->name} has been updated successfully.");
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}   