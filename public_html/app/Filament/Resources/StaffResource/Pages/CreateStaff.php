<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use App\Models\Role;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default values
        $data['is_active'] = $data['is_active'] ?? true;
        
        // Store staff_role temporarily for afterCreate
        $this->staffRole = $data['staff_role'] ?? 'admin';
        unset($data['staff_role']); // Remove from data to save
        
        return $data;
    }

    private string $staffRole = 'admin';

    protected function afterCreate(): void
    {
        // Auto-assign role based on selection
        $role = Role::where('slug', $this->staffRole)->first();
        
        if ($role) {
            $this->record->roles()->syncWithoutDetaching([$role->id]);
        }

        // Send notification
        Notification::make()
            ->title('Staff Created')
            ->body("Staff member {$this->record->name} has been created successfully as " . ucfirst($this->staffRole) . ".")
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}