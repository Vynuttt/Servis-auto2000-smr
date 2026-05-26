<?php

namespace App\Filament\Widgets;

use App\Models\Stall;
use App\Models\WorkOrder;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ActiveStalls extends BaseWidget
{
    /**
     * HANYA TAMPILKAN UNTUK ADMIN/OWNER
     */
    public static function canView(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        /** @var User $user */
        $user = Auth::user();
        return $user->isAdminOrOwner();
    }

    protected static ?string $heading = 'Stalls';
    protected static ?int $sort = 21;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Stall::query()
                    ->where('is_active', true)
                    ->orderBy('code')
            )
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Stall')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('planned_wo_count')
                    ->label('Planned WO')
                    ->badge()
                    ->alignCenter()
                    ->getStateUsing(function ($record) {
                        return WorkOrder::where('stall_id', $record->id)
                            ->where('status', 'Planned')
                            ->count();
                    })
                    ->color(fn ($state): string => $state > 0 ? 'warning' : 'gray'),

                Tables\Columns\TextColumn::make('in_progress_wo')
                    ->label('In-Progress WO • Mechanic')
                    ->getStateUsing(function ($record) {
                        $workOrders = WorkOrder::where('stall_id', $record->id)
                            ->whereIn('status', ['In-Progress', 'Checked-In', 'QC', 'Wash'])
                            ->with('mechanic:id,name,employee_number')
                            ->latest('id')
                            ->get();

                        if ($workOrders->isEmpty()) {
                            return '—';
                        }

                        return $workOrders->map(function ($wo) {
                            $mechanicName = $wo->mechanic?->name ?? 'Unassigned';
                            return "{$wo->wo_number} ({$mechanicName})";
                        })->implode(' • ');
                    })
                    ->wrap()
                    ->html(),
            ])
            ->paginated(false);
    }
}