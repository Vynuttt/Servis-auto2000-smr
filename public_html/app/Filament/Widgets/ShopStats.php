<?php

namespace App\Filament\Widgets;

use App\Models\WorkOrder;
use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // <-- TAMBAHKAN INI

class ShopStats extends BaseWidget
{

    /**
     * HANYA TAMPILKAN UNTUK ADMIN/OWNER
     * (Ini perbaikannya, method-nya diganti)
     */
    public static function canView(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        
        /** @var \App\Models\User $user */
        return $user->isAdminOrOwner();
    }

    protected function getStats(): array
    {
        $today = now()->toDateString();

        $active = WorkOrder::whereNotIn('status', ['Done','Cancelled'])->count();
        $doneToday = WorkOrder::where('status', 'Done')->whereDate('actual_finish', $today)->count();
        $overdue = WorkOrder::whereNotIn('status', ['Done','Cancelled'])
            ->where('planned_finish', '<', now())->count();

       $bookingsToday = Booking::whereDate('booking_date', $today)->count();

        return [
            Stat::make('Active WO', (string)$active)->icon('heroicon-o-bolt'),
            Stat::make('Done Today', (string)$doneToday)->icon('heroicon-o-check-badge')->color('success'),
            Stat::make('Overdue', (string)$overdue)->icon('heroicon-o-exclamation-triangle')->color('danger'),
            Stat::make('Bookings Today', (string)$bookingsToday)->icon('heroicon-o-calendar-days'),
        ];
    }
}
