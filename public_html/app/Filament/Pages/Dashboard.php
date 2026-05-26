<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets;

// 2 WIDGET LAMA ANDA
use App\Filament\Widgets\ShopStats;
use App\Filament\Widgets\TodaysBookings;

// WIDGET BARU
use App\Filament\Widgets\OnDutyMechanics;
use App\Filament\Widgets\ActiveStalls;
use App\Filament\Widgets\ClockInOutWidget;
use App\Filament\Widgets\PendingApprovalsWidget;
use App\Filament\Widgets\MechanicStatsWidget;
use App\Filament\Widgets\CurrentJobWidget;
use App\Filament\Widgets\MyJobQueueWidget;
use App\Filament\Widgets\OnHoldJobsWidget;
class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            // Baris 1: Welcome Card (Account Widget)
           // Widgets\AccountWidget::class,
            
            // Baris 2: 4 Stats Cards (Active WO, Done Today, Overdue, Bookings Today)
            ShopStats::class,
            
            // Baris 3: Clock In/Out Widget (khusus mekanik, jika bukan mekanik tidak muncul)
            ClockInOutWidget::class,
            
            // Baris 4: Today's Bookings (Top 10) - WIDGET INI DULU
            TodaysBookings::class,
            
            // Baris 5: Pending Approvals - BARU WIDGET INI (DI BAWAH TODAY'S BOOKINGS)
            PendingApprovalsWidget::class,
            
            // Baris 6: On-Duty Mechanics
            OnDutyMechanics::class,
            
            // Baris 7: Active Stalls
            ActiveStalls::class,

            MechanicStatsWidget::class,

            CurrentJobWidget::class,     
            MyJobQueueWidget::class,     
            OnHoldJobsWidget::class,
        ];
    }
    
    public function getColumns(): int|string|array
    {
        return 2; // Dashboard menggunakan 2 kolom grid
    }
}