<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Models\Vehicle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationGroup = 'Operations';
    protected static ?string $navigationIcon  = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Vehicles';
    
    // public static function shouldRegisterNavigation(): bool
    //     {
    //         $user = Auth::user();
    //         if (!$user) return false;
            
    //         /** @var \App\Models\User $user */
    //         return $user->isAdminOrOwner();
    //     }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    // ✅ Form configuration — tanpa relationship() agar tidak error
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Vehicle Information')
                ->schema([
                    Forms\Components\Select::make('customer_id')
                        ->label('Customer')
                        ->options(
                            \App\Models\User::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('plate_number')
                        ->label('Plate Number')
                        ->required()
                        ->maxLength(20),

                    Forms\Components\TextInput::make('brand')
                        ->required()
                        ->maxLength(50),

                    Forms\Components\TextInput::make('model')
                        ->required()
                        ->maxLength(50),

                    Forms\Components\TextInput::make('variant')
                        ->maxLength(50),

                    Forms\Components\TextInput::make('year')
                        ->numeric()
                        ->minValue(1980)
                        ->maxValue(2100),

                    Forms\Components\TextInput::make('color')
                        ->maxLength(30),

                    Forms\Components\TextInput::make('chassis_no')
                        ->label('Chassis No')
                        ->maxLength(50),

                    Forms\Components\TextInput::make('engine_no')
                        ->label('Engine No')
                        ->maxLength(50),
                ])
                ->columns(2),
        ]);
    }

    // ✅ Table configuration
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plate_number')
                    ->label('Plate')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('model')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('variant')
                    ->label('Variant'),

                Tables\Columns\TextColumn::make('year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('color')
                    ->label('Color'),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Owner')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->since()
                    ->sortable(),
            ])
            ->filters([]) // tidak ada filter default
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // ✅ Page routes
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVehicles::route('/'),
            // 'create' => Pages\CreateVehicle::route('/create'), // bisa diaktifkan lagi nanti
            'edit'   => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }

    // ✅ Query override (hilangkan global scope bawaan)
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }
}
