<?php

namespace App\Filament\Pages;

use App\Models\WorkOrder;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class MechanicJobs extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-wrench';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?string $navigationLabel = 'My Jobs';
    protected static ?string $title           = 'My Jobs';
    protected static string $view             = 'filament.pages.mechanic-jobs';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (! $user) return false;

        /** @var \App\Models\User $user */
        return $user->hasRole('mechanic');
    }

    public array $stats = [];

    public function mount(): void
    {
        $u = Auth::user();
        $base = WorkOrder::query()->where('mechanic_id', $u->id);

        // Perbaikan: hitung in_progress = semua job aktif (bukan hanya status "In Progress")
        $this->stats = [
            'total'       => (clone $base)->count(),
            'in_progress' => (clone $base)
                ->whereNotIn('status', [WorkOrder::S_DONE, WorkOrder::S_CANCELLED])
                ->count(),
            'waiting'     => (clone $base)->where('status', WorkOrder::S_PLANNED)->count(),
            'done_today'  => (clone $base)->where('status', WorkOrder::S_DONE)
                ->whereBetween('updated_at', [Carbon::today(), Carbon::today()->endOfDay()])
                ->count(),
        ];
    }

    private function baseQuery(): Builder
    {
        $u = Auth::user();

        return WorkOrder::query()
            ->with(['customer', 'vehicle', 'currentStage'])
            ->where('mechanic_id', $u?->id ?? 0);
    }

    /** progress hybrid = max(baseline status, interpolasi waktu) */
    private function calcPct(WorkOrder $r): int
    {
        $start = $r->planned_start?->timestamp;
        $end   = $r->planned_finish?->timestamp;
        $now   = now()->timestamp;

        if (!$start || !$end || $end <= $start) return $r->progressPct(); // fallback baseline
        if ($now <= $start) return max(0, $r->progressPct());
        if ($now >= $end)   return 100;

        $timePct = (int) round((($now - $start) / ($end - $start)) * 100);
        $base    = $r->progressPct();
        return max($base, min(100, $timePct));
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->baseQuery())
            ->columns([
                TextColumn::make('wo_number')->label('WO#')->sortable()->searchable(),

                TextColumn::make('vehicle.plate_number')->label('Plate')->sortable()->toggleable(),

                TextColumn::make('customer.name')->label('Customer')->searchable()->toggleable(),

                TextColumn::make('priority')->badge()->label('Priority')
                    ->color(fn (string $state) => match ($state) {
                        'Urgent'  => 'danger',
                        'Rework'  => 'warning',
                        default   => 'primary',
                    }),

                TextColumn::make('status')->badge()->label('Status')
                    ->color(fn (string $state) => match ($state) {
                        WorkOrder::S_IN_PROGRESS => 'primary',
                        WorkOrder::S_WAITING     => 'warning',
                        WorkOrder::S_QC          => 'info',
                        WorkOrder::S_WASH        => 'info',
                        WorkOrder::S_FINAL       => 'success',
                        WorkOrder::S_DONE        => 'success',
                        WorkOrder::S_CANCELLED   => 'gray',
                        default                  => 'secondary',
                    }),


                TextColumn::make('planned_start')->label('Start')->dateTime('Y-m-d H:i')->sortable(),
                TextColumn::make('planned_finish')->label('ETA')->dateTime('Y-m-d H:i')->sortable(),

                ViewColumn::make('progress_pct')
                    ->label('Progress')
                    ->getStateUsing(fn (WorkOrder $r) => $this->calcPct($r))
                    ->view('components.progress-bar'),
            ])

            ->filters([
                SelectFilter::make('bucket')->label('Bucket')->options([
                        'active'     => 'Active',
                        'done'       => 'Done',
                        'cancelled'  => 'Cancelled',
                        'all'        => 'All',
                    ])->default('active')
                    ->query(function (Builder $q, array $data) {
                        return match ($data['value'] ?? 'active') {
                            'done'      => $q->where('status', WorkOrder::S_DONE),
                            'cancelled' => $q->where('status', WorkOrder::S_CANCELLED),
                            'active'    => $q->whereNotIn('status', [WorkOrder::S_DONE, WorkOrder::S_CANCELLED]),
                            default     => $q,
                        };
                    })
                    ->indicateUsing(fn (array $data) => 'Bucket: ' . ucfirst($data['value'] ?? 'active')),

                SelectFilter::make('scope')->label('Scope')->options([
                        'active'  => 'Active Only',
                        'all'     => 'All',
                        'today'   => 'Today',
                        'overdue' => 'Overdue',
                    ])->default('active')
                    ->query(function (Builder $query, array $data): Builder {
                        $val = $data['value'] ?? 'active';
                        return $query
                            ->when($val === 'active', fn ($q) => $q->whereNotIn('status', [WorkOrder::S_DONE, WorkOrder::S_CANCELLED]))
                            ->when($val === 'today', fn ($q) => $q->whereDate('planned_start', today()))
                            ->when($val === 'overdue', fn ($q) => $q->where('planned_finish', '<', now())
                                ->whereNotIn('status', [WorkOrder::S_DONE, WorkOrder::S_CANCELLED]));
                    })
                    ->indicateUsing(fn (array $data) => match ($data['value'] ?? 'active') {
                        'active'  => 'Scope: Active Only',
                        'today'   => 'Scope: Today',
                        'overdue' => 'Scope: Overdue',
                        default   => 'Scope: All',
                    }),

                SelectFilter::make('status')->multiple()->options([
                    WorkOrder::S_PLANNED     => WorkOrder::S_PLANNED,
                    WorkOrder::S_CHECKED_IN  => WorkOrder::S_CHECKED_IN,
                    WorkOrder::S_WAITING     => WorkOrder::S_WAITING,
                    WorkOrder::S_IN_PROGRESS => WorkOrder::S_IN_PROGRESS,
                    WorkOrder::S_QC          => WorkOrder::S_QC,
                    WorkOrder::S_WASH        => WorkOrder::S_WASH,
                    WorkOrder::S_FINAL       => WorkOrder::S_FINAL,
                    WorkOrder::S_DONE        => WorkOrder::S_DONE,
                    WorkOrder::S_CANCELLED   => WorkOrder::S_CANCELLED,
                ]),
            ])

            ->actions([
                // Semua aksi berikut adalah REQUEST (bukan ubah status langsung)
                Action::make('req_start')
                    ->label('Request Start')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->visible(fn (WorkOrder $r) =>
                        ! $r->pendingRequestedStatus() &&
                        in_array($r->status, [WorkOrder::S_PLANNED, WorkOrder::S_CHECKED_IN, WorkOrder::S_WAITING], true)
                    )
                    ->requiresConfirmation()
                    ->action(fn (WorkOrder $r) => $r->requestTransition(WorkOrder::S_IN_PROGRESS, 'Request Start')),

                Action::make('req_hold')
                    ->label('Request Hold')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->visible(fn (WorkOrder $r) =>
                        ! $r->pendingRequestedStatus() &&
                        in_array($r->status, [WorkOrder::S_IN_PROGRESS, WorkOrder::S_QC, WorkOrder::S_WASH, WorkOrder::S_FINAL], true)
                    )
                    ->requiresConfirmation()
                    ->action(fn (WorkOrder $r) => $r->requestTransition(WorkOrder::S_WAITING, 'Request Hold')),

                Action::make('req_qc')
                    ->label('Request QC')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('info')
                    ->visible(fn (WorkOrder $r) => ! $r->pendingRequestedStatus() && $r->status === WorkOrder::S_IN_PROGRESS)
                    ->action(fn (WorkOrder $r) => $r->requestTransition(WorkOrder::S_QC, 'Request QC')),

                Action::make('req_wash')
                    ->label('Request Wash')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->visible(fn (WorkOrder $r) =>
                        ! $r->pendingRequestedStatus() &&
                        in_array($r->status, [WorkOrder::S_IN_PROGRESS, WorkOrder::S_QC], true)
                    )
                    ->action(fn (WorkOrder $r) => $r->requestTransition(WorkOrder::S_WASH, 'Request Wash')),

                Action::make('req_final')
                    ->label('Request Final')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (WorkOrder $r) =>
                        ! $r->pendingRequestedStatus() &&
                        in_array($r->status, [WorkOrder::S_QC, WorkOrder::S_WASH], true)
                    )
                    ->action(fn (WorkOrder $r) => $r->requestTransition(WorkOrder::S_FINAL, 'Request Final')),

                Action::make('req_done')
                    ->label('Request Done')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (WorkOrder $r) =>
                        ! $r->pendingRequestedStatus() &&
                        in_array($r->status, [WorkOrder::S_IN_PROGRESS, WorkOrder::S_QC, WorkOrder::S_WASH, WorkOrder::S_FINAL], true)
                    )
                    ->requiresConfirmation()
                    ->action(fn (WorkOrder $r) => $r->requestTransition(WorkOrder::S_DONE, 'Request Done')),

                // Logs
                Action::make('Logs')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->label('Logs')
                    ->modalHeading(fn (WorkOrder $r) => 'Activity Log — ' . $r->wo_number)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('2xl')
                    ->modalContent(fn (WorkOrder $r) => view('components.work-order-log-modal', [
                        'record' => $r,
                        'logs'   => $r->logs()->with(['user:id,name'])->latest()->get(),
                    ])),
            ])
            ->defaultSort('planned_start', 'asc')
            ->emptyStateHeading('Belum ada pekerjaan')
            ->emptyStateDescription('Job yang ditugaskan ke Anda akan tampil di sini.')
            ->emptyStateIcon('heroicon-o-wrench-screwdriver');
    }
}
