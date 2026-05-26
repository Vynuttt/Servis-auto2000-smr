<?php

namespace App\Filament\Widgets;

use App\Models\User; // <-- Tambahkan ini
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OnDutyMechanics extends BaseWidget
{
    /**
     * HANYA TAMPILKAN UNTUK ADMIN/OWNER
     * (Ini perbaikannya)
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

    protected static ?string $heading = 'On-Duty Mechanics';
    protected static ?int $sort = 20;            // urutan di dashboard
    protected int|string|array $columnSpan = 'full';
    
    // TAMBAHAN: Ini agar bisa di-refresh oleh widget ClockInOutWidget
    protected static ?string $widgetId = 'OnDutyMechanics';

    /**
     * Set default sort
     */
    protected function getDefaultTableSortColumn(): ?string
    {
        return 'active_wo_count';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    /**
     * Nonaktifkan paginasi
     */
    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    /**
     * Query data untuk tabel (V2 Syntax)
     */
    protected function getTableQuery(): Builder
    {
        $activeWoStatuses = ['Checked-In','Waiting','In-Progress','QC','Wash','Final'];

        return User::query()
            // --- PERUBAHAN UTAMA ADA DI SINI ---
            ->where('is_on_duty', true) // Hanya ambil yang sudah Clock-In
            // --- SELESAI ---
            ->whereHas('roles', fn ($q) => $q->where('slug', 'mechanic'))
            // hitung WO aktif (boleh 0)
            ->withCount([
                'assignedWorkOrders as active_wo_count' => fn ($q) =>
                    $q->whereIn('status', $activeWoStatuses),
            ])
            // ambil sampai 3 WO terakhir (kalau ada) untuk preview
            ->with([
                'assignedWorkOrders' => fn ($q) =>
                    $q->select(['id','wo_number','mechanic_id','status'])
                      ->whereIn('status', $activeWoStatuses)
                      ->latest('id')->limit(3),
            ]);
    }

    /**
     * Definisi kolom tabel (V2 Syntax)
     */
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\ImageColumn::make('avatar')
                ->label(' ')
                ->circular()
                ->height(36)
                ->defaultImageUrl(url('/images/avatar-placeholder.png')),

            Tables\Columns\TextColumn::make('name')
                ->label('Mechanic')
                ->weight('semibold')
                ->searchable(),

            Tables\Columns\TextColumn::make('employee_number')
                ->label('Emp. No')
                ->badge()
                ->formatStateUsing(fn ($state) => $state ?: '-') // Perbaikan $v -> $state
                ->toggleable(),

            Tables\Columns\TextColumn::make('active_wo_count')
                ->label('Active WO')
                ->alignCenter()
                ->badge(),

           Tables\Columns\TextColumn::make('assignedWorkOrders')
            ->label('WO Preview')
            // HANYA GUNAKAN $record, HAPUS $_
            ->formatStateUsing(function ($record) { 
                $list = $record->assignedWorkOrders->map(
                    fn ($wo) => $wo->wo_number
                )->all();

                return empty($list) ? '—' : implode(' • ', $list);
            })
            ->wrap()
            ->toggleable(),

            Tables\Columns\TextColumn::make('status_badge')
                ->label('Status')
                ->getStateUsing(fn ($record) => $record->active_wo_count > 0 ? 'Working' : 'Idle') // V2 menggunakan getStateUsing
                ->badge()
                ->color(fn ($record) => $record->active_wo_count > 0 ? 'success' : 'gray'),
        ];
    }
}

