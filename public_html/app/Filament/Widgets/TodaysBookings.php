<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;        


class TodaysBookings extends BaseWidget
{
    // PERUBAHAN: Ganti dari 'full' (jika ada) menjadi 1
    protected int|string|array $columnSpan = 1;
    
    // OPSIONAL: Atur urutan untuk memastikan posisi
    protected static ?int $sort = 4;

    protected static ?string $heading = "Today's Bookings (Top 10)";

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
        return $table
            ->query(
                Booking::query()
                    ->whereDate('booking_date', today())
                    ->with(['customer', 'vehicle', 'mechanic'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('vehicle.plate_number')
                    ->label('Plate')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('booking_time')
                    ->label('Time')
                    ->time('H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(function ($record) {
                        return match ($record->status) {
                            'Booked' => 'info',
                            'Checked-In' => 'warning',
                            'In Service' => 'primary',
                            'Completed' => 'success',
                            'Cancelled' => 'danger',
                            default => 'gray',
                        };
                    }),
            ])
            ->defaultSort('booking_time', 'asc');
    }
}