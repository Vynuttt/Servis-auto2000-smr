<?php

namespace App\Filament\Resources\StallResource\Pages;

use App\Filament\Resources\StallResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStalls extends ListRecords
{
    protected static string $resource = StallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}