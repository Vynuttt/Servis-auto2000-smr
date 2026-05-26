<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkOrder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\User;


class OnHoldJobsWidget extends BaseWidget
{
    protected static ?int $sort = 3; // Urutan di dashboard
    protected int | string | array $columnSpan = 'full';
    
    // Dengarkan event 'jobStatusChanged'
    protected $listeners = ['jobStatusChanged' => '$refresh'];

    // Hanya tampilkan jika dia mekanik
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
        return 'Pekerjaan Tertunda (On Hold)';
    }

    public function table(Table $table): Table
    {
        $query = WorkOrder::query()
            ->where('mechanic_id', Auth::id())
            ->where('status', 'On Hold'); // Hanya ambil yang 'On Hold'

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('wo_number')->label('No. WO'),
                TextColumn::make('booking.vehicle_plate')->label('Plat Nomor'),
                TextColumn::make('notes') // Tampilkan catatan (alasan ditunda)
                    ->label('Catatan/Alasan')
                    ->limit(50)
                    ->tooltip(fn(WorkOrder $record) => $record->notes),
            ])
            ->actions([
                Action::make('resumeJob')
                    ->label('Lanjutkan')
                    ->icon('heroicon-o-play-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Lanjutkan Pekerjaan')
                    ->action(function (WorkOrder $record) {
                        // Cek apakah ada pekerjaan lain yang 'In Progress'
                        $existingJob = WorkOrder::where('mechanic_id', Auth::id())
                            ->where('status', 'In Progress')
                            ->exists();
                            
                        if ($existingJob) {
                            Notification::make()
                                ->title('Gagal Melanjutkan')
                                ->body('Selesaikan dulu pekerjaan Anda yang sedang aktif.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Update status
                        $record->status = 'In Progress';
                        $record->notes = ($record->notes ?? '') . "\n[Dilanjutkan]";
                        $record->save();
                        
                        Notification::make()->title('Pekerjaan Dilanjutkan!')->success()->send();
                        $this->dispatch('jobStatusChanged'); 
                    }),
            ]);
    }
}