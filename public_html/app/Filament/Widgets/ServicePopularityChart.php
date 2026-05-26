<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\{Service, BookingService};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // <-- INI PERBAIKANNYA
use Illuminate\Support\Facades\Auth;

class ServicePopularityChart extends ChartWidget
{
    protected static ?string $heading = 'Layanan Paling Populer (This Month)';
    protected static ?int $sort = 4;
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

    protected function getData(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        $popularServices = BookingService::query()
            ->whereHas('booking', fn($q) => $q->where('created_at', '>=', $startOfMonth))
            // Anda bisa menggunakan DB::raw() sekarang setelah di-import
            ->select('service_id', DB::raw('COUNT(*) as total')) 
            ->groupBy('service_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('service')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Booking',
                    'data' => $popularServices->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6', // blue
                        '#10b981', // green
                        '#f59e0b', // amber
                        '#ef4444', // red
                        '#8b5cf6', // purple
                        '#ec4899', // pink
                        '#06b6d4', // cyan
                        '#84cc16', // lime
                        '#f97316', // orange
                        '#6366f1', // indigo
                    ],
                ],
            ],
            'labels' => $popularServices->pluck('service.name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
