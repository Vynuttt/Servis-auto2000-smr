<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->requiresConfirmation(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load current role for edit form
        $currentRole = $this->record->roles()->whereIn('slug', ['admin', 'owner'])->first();
        $data['staff_role'] = $currentRole?->slug ?? 'admin';
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store staff_role for afterSave
        $this->staffRole = $data['staff_role'] ?? null;
        unset($data['staff_role']);
        
        return $data;
    }

    private ?string $staffRole = null;

    protected function afterSave(): void
    {
        // Update role if changed
        if ($this->staffRole) {
            $role = \App\Models\Role::where('slug', $this->staffRole)->first();
            
            if ($role) {
                // Remove old admin/owner roles, add new one
                $this->record->roles()->detach(
                    \App\Models\Role::whereIn('slug', ['admin', 'owner'])->pluck('id')
                );
                $this->record->roles()->attach($role->id);
            }
        }
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Staff Updated')
            ->body("Staff member {$this->record->name} has been updated successfully.");
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}