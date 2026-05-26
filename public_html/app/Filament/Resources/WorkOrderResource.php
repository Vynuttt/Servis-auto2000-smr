<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkOrderResource\Pages;
use App\Models\{Booking, Stall, User, Vehicle, WorkOrder, WorkOrderLog, WorkOrderStage};
use Filament\{Forms, Tables};
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class WorkOrderResource extends \Filament\Resources\Resource
{
    protected static ?string $model = WorkOrder::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?string $navigationLabel = 'Work Orders';

    /** Tampilkan menu hanya untuk admin/owner */
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (! $user) return false;

        /** @var \App\Models\User $user */
        return $user->isAdminOrOwner();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Work Order Information')
                ->schema([
                    // Booking (optional)
                    Forms\Components\Select::make('booking_id')
                        ->label('From Booking (optional)')
                        ->searchable()
                        ->preload()
                        ->options(fn () => Booking::latest('id')
                            ->with(['customer', 'vehicle'])
                            ->get()
                            ->mapWithKeys(fn ($b) => [
                                $b->id => "{$b->booking_code} • " .
                                    ($b->customer->name ?? '-') . " • " .
                                    ($b->vehicle->plate_number ?? '-'),
                            ])
                            ->toArray()
                        )
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if (! $state) {
                                $set('customer_id', null);
                                $set('vehicle_id', null);
                                $set('planned_start', null);
                                $set('sla_minutes', 120);
                                return;
                            }

                            $b = Booking::with(['customer', 'vehicle'])->find($state);
                            if ($b) {
                                // Auto-fill customer & vehicle
                                $set('customer_id', $b->customer_id);
                                $set('vehicle_id',  $b->vehicle_id);

                                // Auto-fill planned start
                                if ($b->scheduled_at) {
                                    $set('planned_start', Carbon::parse($b->scheduled_at));
                                } elseif ($b->booking_date && $b->booking_time) {
                                    $set('planned_start', Carbon::parse("{$b->booking_date} {$b->booking_time}"));
                                } elseif ($b->booking_date) {
                                    $set('planned_start', Carbon::parse($b->booking_date)->setTime(8, 0));
                                }

                                // Auto-fill SLA
                                $sla = $b->sla_minutes ?? 120;
                                $set('sla_minutes', $sla);

                                // Auto-fill notes dari complaint
                                if ($b->complaint_note) {
                                    $set('notes', $b->complaint_note);
                                }

                                // Auto-calculate planned_finish
                                $start = $get('planned_start');
                                if ($start) {
                                    $set('planned_finish', Carbon::parse($start)->addMinutes($sla));
                                }
                            }
                        })
                        ->helperText('Pilih booking untuk auto-fill data'),

                    // Customer
                    Forms\Components\Select::make('customer_id')
                        ->label('Customer')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->relationship('customer', 'name')
                        ->disabled(fn ($get) => filled($get('booking_id')))
                        ->dehydrated(true),

                    // Vehicle (filter by customer)
                    Forms\Components\Select::make('vehicle_id')
                        ->label('Vehicle')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->options(fn ($get) => $get('customer_id')
                            ? Vehicle::where('customer_id', $get('customer_id'))
                                ->orderBy('plate_number')
                                ->get()
                                ->mapWithKeys(fn ($v) => [
                                    $v->id => "{$v->plate_number} • {$v->brand} {$v->model} ({$v->year})",
                                ])
                                ->toArray()
                            : []
                        )
                        ->disabled(fn ($get) => filled($get('booking_id')))
                        ->dehydrated(true),
                ])
                ->columns(3),

            Forms\Components\Section::make('Assignment')
                ->description('⚠️ WAJIB: Pilih mechanic dan stall untuk WO ini')
                ->schema([
                    // Mechanic - REQUIRED
                    Forms\Components\Select::make('mechanic_id')
                        ->label('Assigned Mechanic')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn () =>
                            User::whereHas('roles', fn ($q) => $q->where('slug', 'mechanic'))
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->placeholder('Pilih mechanic...')
                        ->helperText('Mechanic yang akan mengerjakan WO ini'),

                    // Stall - REQUIRED
                    Forms\Components\Select::make('stall_id')
                        ->label('Service Stall')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->options(fn () => Stall::orderBy('name')->pluck('name', 'id')->toArray())
                        ->placeholder('Pilih stall...')
                        ->helperText('Stall tempat mengerjakan WO'),
                ])
                ->columns(2)
                ->collapsed(false), // Selalu expand agar terlihat

            Forms\Components\Section::make('Schedule & Priority')
                ->schema([
                    // Planned Start
                    Forms\Components\DateTimePicker::make('planned_start')
                        ->label('Planned Start')
                        ->seconds(false)
                        ->native(false)
                        ->required()
                        ->default(now())
                        ->live()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $sla = (int) ($get('sla_minutes') ?: 120);
                            if ($state && $sla > 0) {
                                $finish = Carbon::parse($state)->copy()->addMinutes($sla);
                                $set('planned_finish', $finish);
                            }
                        }),

                    // SLA Minutes - USER INPUT
                    Forms\Components\TextInput::make('sla_minutes')
                        ->label('SLA Duration (minutes)')
                        ->numeric()
                        ->default(120)
                        ->required()
                        ->minValue(15)
                        ->maxValue(960)
                        ->suffix('menit')
                        ->live()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $start = $get('planned_start');
                            if ($start && $state) {
                                $finish = Carbon::parse($start)->copy()->addMinutes((int) $state);
                                $set('planned_finish', $finish);
                            }
                        })
                        ->helperText('Estimasi waktu pengerjaan'),

                    // Planned Finish - AUTO CALCULATED
                    Forms\Components\DateTimePicker::make('planned_finish')
                        ->label('Planned Finish')
                        ->seconds(false)
                        ->native(false)
                        ->disabled()
                        ->dehydrated()
                        ->helperText('Otomatis: Start + SLA'),

                    // Priority
                    Forms\Components\Select::make('priority')
                        ->label('Priority Level')
                        ->required()
                        ->options([
                            'Regular' => '🟢 Regular',
                            'Urgent'  => '🟡 Urgent',
                            'Rework'  => '🔴 Rework',
                        ])
                        ->default('Regular'),

                    // Status
                    Forms\Components\Select::make('status')
                        ->label('Initial Status')
                        ->required()
                        ->options([
                            WorkOrder::S_PLANNED     => '📋 Planned',
                            WorkOrder::S_CHECKED_IN  => '✅ Checked In',
                        ])
                        ->default(WorkOrder::S_PLANNED),

                    // Notes
                    Forms\Components\Textarea::make('notes')
                        ->label('Notes / Keluhan Customer')
                        ->rows(4)
                        ->placeholder('Catatan keluhan atau instruksi khusus...')
                        ->columnSpanFull(),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wo_number')
                    ->label('WO#')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('WO number copied!')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('vehicle.plate_number')
                    ->label('Plate')
                    ->searchable(),

                Tables\Columns\TextColumn::make('mechanic.name')
                    ->label('Mechanic')
                    ->toggleable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('stall.name')
                    ->label('Stall')
                    ->toggleable()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('currentStage.name')
                    ->label('Stage')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'Reception' => 'gray',
                        'Diagnosis' => 'info',
                        'Repair' => 'warning',
                        'QC' => 'success',
                        'Completed' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('planned_start')
                    ->label('Start')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('planned_finish')
                    ->label('Finish')
                    ->dateTime('Y-m-d H:i'),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'Regular' => 'success',
                        'Urgent' => 'warning',
                        'Rework' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        WorkOrder::S_DONE => 'success',
                        WorkOrder::S_CANCELLED => 'danger',
                        WorkOrder::S_IN_PROGRESS => 'warning',
                        WorkOrder::S_QC => 'info',
                        default => 'gray',
                    }),

                // Pending request badge
                Tables\Columns\TextColumn::make('pending_request')
                    ->label('Pending')
                    ->state(fn (WorkOrder $r) => $r->pendingRequestedStatus())
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state ? "📝 {$state}" : null),
            ])
            ->defaultSort('planned_start', 'desc')

            ->filters([
                // SCOPE
                Tables\Filters\SelectFilter::make('scope')
                    ->label('Quick Filter')
                    ->options([
                        'all'      => 'All Work Orders',
                        'today'    => 'Today',
                        'tomorrow' => 'Tomorrow',
                        'week'     => 'This Week',
                        'next7'    => 'Next 7 Days',
                        'overdue'  => 'Overdue',
                    ])
                    ->default('today')
                    ->query(function (Builder $q, array $data) {
                        $v = $data['value'] ?? null;
                        if (! $v || $v === 'all') return $q;

                        return match ($v) {
                            'today'    => $q->whereDate('planned_start', now()->toDateString()),
                            'tomorrow' => $q->whereDate('planned_start', now()->copy()->addDay()->toDateString()),
                            'week'     => $q->whereBetween('planned_start', [now()->startOfWeek(), now()->endOfWeek()]),
                            'next7'    => $q->whereBetween('planned_start', [now()->startOfDay(), now()->copy()->addDays(7)->endOfDay()]),
                            'overdue'  => $q->where('planned_finish', '<', now())->whereNotIn('status', [WorkOrder::S_DONE, WorkOrder::S_CANCELLED]),
                            default    => $q,
                        };
                    }),

                // DATE RANGE
                Tables\Filters\Filter::make('date_range')
                    ->label('Date Range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From')->native(false),
                        Forms\Components\DatePicker::make('to')->label('To')->native(false),
                    ])
                    ->query(function (Builder $q, array $data) {
                        if (!empty($data['from'])) {
                            $q->whereDate('planned_start', '>=', $data['from']);
                        }
                        if (!empty($data['to'])) {
                            $q->whereDate('planned_start', '<=', $data['to']);
                        }
                    }),

                // STATUS
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->multiple()
                    ->options([
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

                // MECHANIC
                Tables\Filters\SelectFilter::make('mechanic_id')
                    ->label('Mechanic')
                    ->relationship('mechanic', 'name')
                    ->searchable()
                    ->preload(),

                // STALL
                Tables\Filters\SelectFilter::make('stall_id')
                    ->label('Stall')
                    ->relationship('stall', 'name')
                    ->searchable()
                    ->preload(),

                // STAGE
                Tables\Filters\SelectFilter::make('current_stage_id')
                    ->label('Current Stage')
                    ->relationship('currentStage', 'name')
                    ->preload(),
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                  Tables\Actions\Action::make('print')
                    ->label('Print PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (WorkOrder $record): string => route('admin.work-orders.print', $record->id))
                    ->openUrlInNewTab(),

                // === Approval Actions ===
                Tables\Actions\Action::make('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-badge')
                    ->visible(fn (WorkOrder $record) => (bool) $record->pendingRequestedStatus())
                    ->requiresConfirmation()
                    ->modalHeading('Approve Status Change')
                    ->modalDescription(fn (WorkOrder $record) => "Approve change to: {$record->pendingRequestedStatus()}")
                    ->action(function (WorkOrder $record) {
                        $to = $record->approveLatestRequest('Approved via admin');
                        if (! $to) {
                            Notification::make()
                                ->title('No pending request')
                                ->danger()
                                ->send();
                            return;
                        }
                        Notification::make()
                            ->title("✅ Approved → {$to}")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (WorkOrder $record) => (bool) $record->pendingRequestedStatus())
                    ->requiresConfirmation()
                    ->modalHeading('Reject Status Change')
                    ->modalDescription(fn (WorkOrder $record) => "Reject change to: {$record->pendingRequestedStatus()}")
                    ->action(function (WorkOrder $record) {
                        $rejected = $record->rejectLatestRequest('Rejected via admin');
                        if (! $rejected) {
                            Notification::make()
                                ->title('No pending request')
                                ->danger()
                                ->send();
                            return;
                        }
                        Notification::make()
                            ->title("❌ Request Rejected")
                            ->danger()
                            ->send();
                    }),
            ])

            

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWorkOrders::route('/'),
            'create' => Pages\CreateWorkOrder::route('/create'),
            'edit'   => Pages\EditWorkOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $today = WorkOrder::whereDate('planned_start', now()->toDateString())
            ->whereNotIn('status', [WorkOrder::S_DONE, WorkOrder::S_CANCELLED])
            ->count();
        
        return $today > 0 ? (string) $today : null;
    }
}