<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ClockInOutWidget extends Widget
{
    protected static string $view = 'filament.widgets.clock-in-out-widget';
    protected int|string|array $columnSpan = 'full';
    
    public bool $isOnDuty = false;

    /**
     * Cek status saat ini saat widget dimuat
     */
    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->isOnDuty = $user->is_on_duty ?? false;
    }

    /**
     * Hanya tampilkan widget ini untuk mekanik
     */
    public static function canView(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        /** @var User $user */
        $user = Auth::user();
        return $user->hasRole('mechanic');
    }

    /**
     * Aksi yang dipanggil oleh tombol
     */
    public function toggleClockIn(): void
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Toggle status
        $user->is_on_duty = !$user->is_on_duty;
        $user->save();

        // Update properti di widget agar tombol berubah
        $this->isOnDuty = $user->is_on_duty;
        
        // Kirim notifikasi
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->isOnDuty ? 'Anda berhasil Clock In!' : 'Anda berhasil Clock Out.',
        ]);
        
        // Refresh widget OnDutyMechanics
        $this->dispatch('refresh-widget-OnDutyMechanics');
    }
}