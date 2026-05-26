<?php

namespace App\Filament\Resources\MechanicResource\Pages;

use App\Filament\Resources\MechanicResource;
use App\Models\Role;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateMechanic extends CreateRecord
{
    protected static string $resource = MechanicResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values jika perlu
        $data['is_active'] = $data['is_active'] ?? true;
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Auto-assign role 'mechanic' setelah user dibuat
        $mechanicRole = Role::where('slug', 'mechanic')->first();
        
        if ($mechanicRole) {
            $this->record->roles()->syncWithoutDetaching([$mechanicRole->id]);
        }

        // Send notification
        Notification::make()
            ->title('Mechanic Created')
            ->body("Mechanic {$this->record->name} has been created successfully.")
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}