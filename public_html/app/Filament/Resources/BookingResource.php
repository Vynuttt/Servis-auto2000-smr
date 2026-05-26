<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\WorkOrderResource;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationGroup = 'Operations';
    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Bookings';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        
        /** @var \App\Models\User $user */
        return $user->isAdminOrOwner();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Customer & Vehicle')
                ->schema([
                    Forms\Components\Select::make('customer_id')
                        ->label('Customer')
                        ->options(
                            User::whereHas('roles', fn ($r) => $r->where('slug', 'customer'))
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('vehicle_id', null)),

                    Forms\Components\Select::make('vehicle_id')
                        ->label('Vehicle')
                        ->required()
                        ->preload()
                        ->searchable()
                        ->options(function (callable $get) {
                            $customerId = $get('customer_id');
                            if (!$customerId) return [];
                            return Vehicle::where('customer_id', $customerId)
                                ->orderBy('plate_number')
                                ->get()
                                ->mapWithKeys(fn ($v) => [
                                    $v->id => "{$v->plate_number} — {$v->brand} {$v->model} {$v->year}"
                                ])->toArray();
                        })
                        ->disabled(fn (callable $get) => blank($get('customer_id')))
                        ->live()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('booking_time', null)),
                ])->columns(2),

            Forms\Components\Section::make('Schedule & Service')
                ->schema([
                    Forms\Components\DatePicker::make('booking_date')
                        ->label('Booking date')
                        ->required()
                        ->minDate(now()->toDateString())
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('booking_time', null)),

                    Forms\Components\Select::make('mechanic_id')
                        ->label('Mechanic (optional)')
                        ->options(
                            User::whereHas('roles', fn ($r) => $r->where('slug', 'mechanic'))
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('booking_time', null)),

                    Forms\Components\TimePicker::make('booking_time')
                        ->label('Booking time')
                        ->required()
                        ->native(false)
                        ->seconds(false)
                        ->minutesStep(15)
                        ->rule(function (callable $get, ?Booking $record) {
                            $vehicleId = $get('vehicle_id');
                            $date      = $get('booking_date');
                            if (!$vehicleId || !$date) return null;

                            return Rule::unique('bookings', 'booking_time')
                                ->where(fn ($q) => $q
                                    ->where('vehicle_id', $vehicleId)
                                    ->whereDate('booking_date', $date)
                                )
                                ->ignore($record?->getKey());
                        })
                        ->validationMessages([
                            'unique' => 'Slot waktu bentrok. Ganti waktu / mekanik.',
                        ])
                        ->helperText('Sistem menolak jadwal bentrok untuk kendaraan yang sama.'),

                    Forms\Components\TextInput::make('service_type')
                        ->label('Service Type')
                        ->placeholder('Servis Berkala / AC Repair / dll')
                        ->required(),

                    Forms\Components\Textarea::make('notes')->columnSpanFull(),

                    Forms\Components\Select::make('status')
                        ->options([
                            'Booked'      => 'Booked',
                            'Checked-In'  => 'Checked-In',
                            'In Service'  => 'In Service',
                            'Completed'   => 'Completed',
                            'Cancelled'   => 'Cancelled',
                        ])
                        ->default('Booked')
                        ->required(),
                ])->columns(2),

            Forms\Components\TextInput::make('booking_code')
                ->label('Booking Code')
                ->disabled()
                ->dehydrated(false)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('Code')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Booking code copied!')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('vehicle.plate_number')
                    ->label('Plate')
                    ->searchable(),

                Tables\Columns\TextColumn::make('service_type')
                    ->label('Service type')
                    ->limit(24),

                Tables\Columns\TextColumn::make('booking_date')
                    ->label('Booking date')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking_time')
                    ->label('Time')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'Booked' => 'warning',
                        'Checked-In' => 'info',
                        'In Service' => 'primary',
                        'Completed' => 'success',
                        'Cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('has_wo')
                    ->label('WO')
                    ->state(fn (Booking $record) => (bool) $record->workOrder)
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->label('Created')
                    ->toggleable(),
            ])
            ->defaultSort('booking_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Booked'      => 'Booked',
                        'Checked-In'  => 'Checked-In',
                        'In Service'  => 'In Service',
                        'Completed'   => 'Completed',
                        'Cancelled'   => 'Cancelled',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('has_wo')
                    ->label('Has Work Order')
                    ->query(fn (Builder $query) => $query->whereHas('workOrder'))
                    ->toggle(),

                Tables\Filters\Filter::make('no_wo')
                    ->label('No Work Order')
                    ->query(fn (Builder $query) => $query->whereDoesntHave('workOrder'))
                    ->toggle(),
            ])
            ->actions([
                // Create WO
                Tables\Actions\Action::make('create_wo')
                    ->label('Create WO')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->visible(fn (Booking $record) => 
                        $record->status === 'Booked' && !$record->workOrder
                    )
                    ->url(fn (Booking $record) => 
                        WorkOrderResource::getUrl('create', ['booking_id' => $record->id])
                    )
                    ->tooltip('Create Work Order from this booking'),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                // Print PDF
                Action::make('print')
                    ->label('Print PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (Booking $record): string => route('admin.bookings.print', $record->id))
                    ->openUrlInNewTab(),
                
                // Cancel
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Booking $record) => 
                        in_array($record->status, ['Booked', 'Checked-In']) && !$record->workOrder
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Booking')
                    ->modalDescription(fn (Booking $record) => 
                        "Are you sure you want to cancel booking {$record->booking_code}?"
                    )
                    ->action(function (Booking $record) {
                        try {
                            $record->cancel('Cancelled by admin');
                            
                            Notification::make()
                                ->title('Booking Cancelled')
                                ->body("Booking {$record->booking_code} has been cancelled.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Booking $record) => !$record->workOrder),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['customer', 'vehicle', 'mechanic', 'workOrder']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit'   => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = Booking::where('status', 'Booked')
            ->whereDoesntHave('workOrder')
            ->count();
        
        return $pending > 0 ? (string) $pending : null;
    }
}