<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\{User, WorkOrder};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MechanicPerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Mechanic Performance (This Month)';


    public static function canView(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        
        /** @var \App\Models\User $user */
        return $user->isAdminOrOwner();
    }

    public function table(Table $table): Table
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        return $table
            ->query(
                User::query()
                    ->whereHas('roles', fn($q) => $q->where('slug', 'mechanic'))
                    ->withCount([
                        'assignedWorkOrders as total_wo' => fn($q) => 
                            $q->where('created_at', '>=', $startOfMonth),
                        'assignedWorkOrders as completed_wo' => fn($q) => 
                            $q->where('status', 'Done')
                              ->where('created_at', '>=', $startOfMonth),
                        'assignedWorkOrders as in_progress_wo' => fn($q) => 
                            $q->whereIn('status', ['In-Progress', 'QC', 'Wash'])
                              ->where('created_at', '>=', $startOfMonth),
                    ])
                    ->withAvg([
                        'assignedWorkOrders as avg_completion_time' => fn($q) => 
                            DB::raw('TIMESTAMPDIFF(HOUR, actual_start, actual_finis)')
                    ], DB::raw('TIMESTAMPDIFF(HOUR, actual_start, actual_finish)'))
                    ->having('total_wo', '>', 0)
                    ->orderByDesc('completed_wo')
            )
            ->columns([
                Tables\Columns\TextColumn::make('employee_number')
                    ->label('ID')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Mechanic')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('total_wo')
                    ->label('Total WO')
                    ->alignCenter()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('completed_wo')
                    ->label('Completed')
                    ->alignCenter()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('in_progress_wo')
                    ->label('In Progress')
                    ->alignCenter()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('completion_rate')
                    ->label('Completion Rate')
                    ->getStateUsing(fn($record) => 
                        $record->total_wo > 0 
                            ? round(($record->completed_wo / $record->total_wo) * 100, 1) . '%'
                            : '0%'
                    )
                    ->badge()
                    ->color(fn($record) => 
                        $record->total_wo > 0 && ($record->completed_wo / $record->total_wo) >= 0.8 
                            ? 'success' 
                            : 'warning'
                    ),

                Tables\Columns\TextColumn::make('avg_completion_time')
                    ->label('Avg Time (hrs)')
                    ->getStateUsing(fn($record) => 
                        $record->avg_completion_time 
                            ? round($record->avg_completion_time, 1) 
                            : '-'
                    )
                    ->alignEnd(),

                Tables\Columns\IconColumn::make('is_on_duty')
                    ->label('On Duty')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->paginated(false);
    }
}