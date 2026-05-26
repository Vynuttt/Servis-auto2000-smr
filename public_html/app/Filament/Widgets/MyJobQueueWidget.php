<?php

namespace App\Filament\Widgets;

// ... (use statements) ...
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class MyJobQueueWidget extends BaseWidget
{
    // ... (properti lain) ...
    protected static ?int $sort = 2; // Urutan di dashboard
    protected int | string | array $columnSpan = 'full';
    
    // Dengarkan event 'jobStatusChanged' dari CurrentJobWidget
    protected $listeners = ['jobStatusChanged' => '$refresh']; 

    // ... (canView() sudah benar) ...
    public static function canView(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        /** @var User $user */
        $user = Auth::user();
        return $user->hasRole('mechanic');
    }
    
    public function getTableHeading(): string
    {
        return 'Antrian Pekerjaan Saya (Hari Ini)';
    }

    public function table(Table $table): Table
    {
        // --- PERBAIKAN 1: Query ---
        // Query ini sekarang HANYA mengambil pekerjaan yang BISA DIMULAI
        // (Yaitu yang statusnya 'Queued' atau 'Planned' atau 'Waiting')
        $query = WorkOrder::query()
            ->where('mechanic_id', Auth::id())
            // Sesuaikan array ini dengan status "siap kerja" Anda
            ->whereIn('status', [
                WorkOrder::S_PLANNED, 
                WorkOrder::S_CHECKED_IN, 
                WorkOrder::S_WAITING
            ]) 
            ->whereDate('planned_start', Carbon::today())
            ->orderBy('planned_start', 'asc'); // Urutkan berdasarkan jam

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('planned_start')
                    ->label('Jam')
                    ->time('H:i'),
                TextColumn::make('booking.vehicle_plate')
                    ->label('Plat Nomor')
                    ->searchable(),
                TextColumn::make('booking.vehicle_model')
                    ->label('Kendaraan'),
                // Ambil daftar layanan. Ini sedikit rumit
                TextColumn::make('booking.services_list') 
                    ->label('Layanan')
                    ->listWithLineBreaks()
                    ->getStateUsing(function (WorkOrder $record) {
                        // Pastikan relasi 'booking' dan 'bookingServices' ada
                        if (!$record->booking) {
                            return ['N/A'];
                        }
                        // Asumsi relasi bookingServices ada di model Booking
                        // Perlu cek null pointer
                        if (!$record->booking->bookingServices) {
                            return ['Data layanan tidak lengkap'];
                        }
                        return $record->booking->bookingServices->map(fn($bs) => $bs->service?->name ?? 'Layanan Dihapus');
                    }),
                // Tambahkan kolom status agar jelas
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                         WorkOrder::S_PLANNED => 'primary',
                         WorkOrder::S_CHECKED_IN => 'primary',
                         WorkOrder::S_WAITING => 'warning',
                         default => 'gray',
                    }),
            ])
            ->actions([
                // --- INI PERBAIKANNYA ---
                // Mengganti tombol "Mulai Kerjakan" (direct action)
                // menjadi "Request Start" (approval action)
                Action::make('req_start')
                    ->label('Request Start')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    // Logika 'visible' disamakan dengan halaman MyJobs.php
                    ->visible(fn (WorkOrder $r) =>
                        ! $r->pendingRequestedStatus() &&
                        in_array($r->status, [WorkOrder::S_PLANNED, WorkOrder::S_CHECKED_IN, WorkOrder::S_WAITING], true)
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Minta Mulai Pekerjaan')
                    
                    // --- PERBAIKAN DI SINI ---
                    // Mengganti modalBody() menjadi modalDescription()
                    ->modalDescription('Ini akan mengirim notifikasi ke Kepala Bengkel untuk persetujuan.')
                    // --- SELESAI ---

                    // Logika 'action' disamakan dengan halaman MyJobs.php
                    ->action(function (WorkOrder $r) {
                        $r->requestTransition(WorkOrder::S_IN_PROGRESS, 'Request Start');
                        Notification::make()->title('Permintaan Terkirim')->success()->send();
                        // Refresh widget ini dan widget lain
                        $this->dispatch('jobStatusChanged'); 
                    }),
            ]);
    }
}