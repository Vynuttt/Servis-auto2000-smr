<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    /** setelah user dibuat, attach role 'customer' ke pivot user_roles */
    protected function afterCreate(): void
    {
        $roleId = DB::table('roles')->where('slug', 'customer')->value('id');
        if ($roleId) {
            $this->record->roles()->syncWithoutDetaching([$roleId]);
        }
    }
}
