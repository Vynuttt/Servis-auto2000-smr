<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\{Booking, WorkOrder, User};
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AnalyticsDashboardWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';


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
        // This Month
        $thisMonthStart = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // // Total Revenue This Month
        // $thisMonthRevenue = WorkOrder::where('status', 'Done')
        //     ->where('created_at', '>=', $thisMonthStart)
        //     ->sum('total_cost');

        // $lastMonthRevenue = WorkOrder::where('status', 'Done')
        //     ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
        //     ->sum('total_cost');

        // $revenueChange = $lastMonthRevenue > 0 
        //     ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
        //     : 0;

        // Total Bookings This Month
        $thisMonthBookings = Booking::where('created_at', '>=', $thisMonthStart)->count();
        $lastMonthBookings = Booking::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
        $bookingChange = $lastMonthBookings > 0 
            ? round((($thisMonthBookings - $lastMonthBookings) / $lastMonthBookings) * 100, 1)
            : 0;

        // Completed Work Orders This Month
        $thisMonthCompleted = WorkOrder::where('status', 'Done')
            ->where('created_at', '>=', $thisMonthStart)
            ->count();

        $lastMonthCompleted = WorkOrder::where('status', 'Done')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->count();

        $completedChange = $lastMonthCompleted > 0 
            ? round((($thisMonthCompleted - $lastMonthCompleted) / $lastMonthCompleted) * 100, 1)
            : 0;

        // // Average Service Time (hours)
        // $avgServiceTime = WorkOrder::where('status', 'Done')
        //     ->where('created_at', '>=', $thisMonthStart)
        //     ->whereNotNull('actual_start')
        //     ->whereNotNull('actual_end')
        //     ->get()
        //     ->avg(function($wo) {
        //         return $wo->actual_start && $wo->actual_end 
        //             ? $wo->actual_start->diffInHours($wo->actual_end) 
        //             : 0;
        //     });

        // Active Mechanics
        $activeMechanics = User::whereHas('roles', fn($q) => $q->where('slug', 'mechanic'))
            ->where('is_on_duty', true)
            ->count();

        // Customer Satisfaction (example: based on completed WO)
        $satisfactionRate = WorkOrder::where('status', 'Done')
            ->where('created_at', '>=', $thisMonthStart)
            ->count();
        $totalWO = WorkOrder::where('created_at', '>=', $thisMonthStart)->count();
        $satisfactionPercent = $totalWO > 0 ? round(($satisfactionRate / $totalWO) * 100, 1) : 0;

        return [
            // Stat::make('Revenue Bulan Ini', 'Rp ' . number_format($thisMonthRevenue, 0, ',', '.'))
            //     ->description($revenueChange >= 0 ? "+{$revenueChange}% dari bulan lalu" : "{$revenueChange}% dari bulan lalu")
            //     ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            //     ->color($revenueChange >= 0 ? 'success' : 'danger')
            //     ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Total Bookings', $thisMonthBookings)
                ->description($bookingChange >= 0 ? "+{$bookingChange}% dari bulan lalu" : "{$bookingChange}% dari bulan lalu")
                ->descriptionIcon($bookingChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($bookingChange >= 0 ? 'success' : 'danger')
                ->chart([3, 5, 7, 9, 6, 8, 10, 8]),

            Stat::make('WO Selesai', $thisMonthCompleted)
                ->description($completedChange >= 0 ? "+{$completedChange}% dari bulan lalu" : "{$completedChange}% dari bulan lalu")
                ->descriptionIcon($completedChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($completedChange >= 0 ? 'success' : 'danger')
                ->chart([4, 6, 5, 7, 8, 6, 9, 7]),

            // Stat::make('Avg Service Time', round($avgServiceTime, 1) . ' jam')
            //     ->description('Rata-rata waktu servis')
            //     ->descriptionIcon('heroicon-m-clock')
            //     ->color('info'),

            Stat::make('Mekanik On-Duty', $activeMechanics)
                ->description('Mekanik aktif saat ini')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),

            Stat::make('Completion Rate', $satisfactionPercent . '%')
                ->description('Tingkat penyelesaian WO')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}