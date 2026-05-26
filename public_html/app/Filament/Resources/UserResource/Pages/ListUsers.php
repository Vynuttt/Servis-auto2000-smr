<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn () => User::query()->count()),

            'admins' => Tab::make('Admins / Owners')
                ->modifyQueryUsing(function (Builder $query): Builder {
                    return $query->where(function (Builder $q) {
                        $q->whereHas('roles', fn ($r) => $r->whereIn('slug', ['admin', 'owner']))
                          ->orWhereIn('role_id', [1, 2]); // fallback single-role id
                    });
                })
                ->badge(fn () => User::query()
                    ->where(fn ($q) => $q
                        ->whereHas('roles', fn ($r) => $r->whereIn('slug', ['admin', 'owner']))
                        ->orWhereIn('role_id', [1, 2])
                    )->count()
                ),

            'mechanics' => Tab::make('Mechanics')
                ->modifyQueryUsing(function (Builder $query): Builder {
                    return $query->where(function (Builder $q) {
                        $q->whereHas('roles', fn ($r) => $r->where('slug', 'mechanic'))
                          ->orWhere('role_id', 3);
                    });
                })
                ->badge(fn () => User::query()
                    ->where(fn ($q) => $q
                        ->whereHas('roles', fn ($r) => $r->where('slug', 'mechanic'))
                        ->orWhere('role_id', 3)
                    )->count()
                ),

            'customers' => Tab::make('Customers')
                ->modifyQueryUsing(function (Builder $query): Builder {
                    return $query->where(function (Builder $q) {
                        $q->whereHas('roles', fn ($r) => $r->where('slug', 'customer'))
                          ->orWhere('role_id', 4);
                    });
                })
                ->badge(fn () => User::query()
                    ->where(fn ($q) => $q
                        ->whereHas('roles', fn ($r) => $r->where('slug', 'customer'))
                        ->orWhere('role_id', 4)
                    )->count()
                ),

        ];
    }
}
