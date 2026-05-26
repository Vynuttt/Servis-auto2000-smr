<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkOrder; // Pastikan Anda sudah punya model WorkOrder
use Carbon\Carbon;
use App\Models\User;


class MechanicStatsWidget extends BaseWidget
{
    // Hanya tampilkan widget ini jika user adalah mekanik
    public static function canView(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        /** @var User $user */
        $user = Auth::user();
        return $user->hasRole('mechanic');
    }

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $mechanicId = $user->id;
        $today = Carbon::today();

        // 1. Total Pekerjaan Hari Ini
        // Hitung semua WO untuk mekanik ini yang dijadwalkan hari ini
        $totalJobs = WorkOrder::where('mechanic_id', $mechanicId)
            ->whereDate('planned_start', $today) // Asumsi Anda punya 'planned_start'
            ->count();

        // 2. Pekerjaan Selesai Hari Ini
        // Hitung WO yang statusnya 'Completed' dan diselesaikan hari ini
        $completedJobs = WorkOrder::where('mechanic_id', $mechanicId)
            ->where('status', 'Completed')
            ->whereDate('updated_at', $today) // Asumsi 'updated_at' adalah tgl selesai
            ->count();
            
        // 3. Masih Antri / Dikerjakan
        // Hitung WO hari ini yang statusnya BUKAN 'Completed' atau 'Cancelled'
        $pendingJobs = WorkOrder::where('mechanic_id', $mechanicId)
            ->whereDate('planned_start', $today)
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->count();

        return [
            Stat::make('Total Pekerjaan Hari Ini', $totalJobs)
                ->description('Semua WO yang dijadwalkan')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('gray'),
                
            Stat::make('Pekerjaan Selesai', $completedJobs)
                ->description('Diselesaikan hari ini')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Masih Antri / Dikerjakan', $pendingJobs)
                ->description('WO yang belum selesai')
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}