<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?string $navigationLabel = 'Reports';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.reports';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        
        /** @var \App\Models\User $user */
        return $user->isAdminOrOwner();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_services')
                ->label('Cetak Laporan Layanan')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(route('admin.reports.print-services'), shouldOpenInNewTab: true),
            
// Tombol 2: Laporan Kinerja Mekanik (BARU)
            Action::make('print_mechanics')
                ->label('Cetak Kinerja Mekanik')
                ->icon('heroicon-o-user-group')
                ->color('gray')
                ->url(route('admin.reports.print-mechanics'), shouldOpenInNewTab: true),

            // Tombol 3: Laporan Analitik (BARU)
            Action::make('print_analytics')
                ->label('Cetak Ringkasan Analitik')
                ->icon('heroicon-o-chart-pie')
                ->color('gray')
                ->url(route('admin.reports.print-analytics'), shouldOpenInNewTab: true),
        ];
    }   

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\AnalyticsDashboardWidget::class,
            //\App\Filament\Widgets\RevenueTrendChart::class,
            \App\Filament\Widgets\MechanicPerformanceWidget::class,
            \App\Filament\Widgets\ServicePopularityChart::class,
        ];
    }
}