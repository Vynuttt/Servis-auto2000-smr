<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkOrder;
use App\Models\User;

class CurrentJobWidget extends Widget
{
    protected static string $view = 'filament.widgets.current-job-widget';
    protected int | string | array $columnSpan = 'full';
    
    // Auto-refresh setiap 10 detik
    protected static ?string $pollingInterval = '10s';
    
    public ?WorkOrder $record = null;

    public function mount(): void
    {
        $this->loadCurrentJob();
    }

    public function loadCurrentJob(): void
    {
        $this->record = WorkOrder::where('mechanic_id', Auth::id())
            ->whereNotIn('status', [WorkOrder::S_DONE, WorkOrder::S_CANCELLED])
            ->with(['booking.bookingServices.service', 'vehicle.customer', 'approvals' => function($query) {
                $query->where('status', 'pending')->latest();
            }])
            ->orderBy('planned_start', 'asc')
            ->first();
    }

    public static function canView(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        /** @var User $user */
        $user = Auth::user();
        return $user->hasRole('mechanic');
    }
}