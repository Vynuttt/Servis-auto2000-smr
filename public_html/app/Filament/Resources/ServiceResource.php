<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Operations';
    protected static ?string $navigationLabel = 'Services';
    protected static ?int $navigationSort = 4;

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
            Forms\Components\Section::make('Service Information')
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->label('Service Code')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50)
                        ->placeholder('SVC001')
                        ->helperText('Kode unik untuk layanan'),

                    Forms\Components\TextInput::make('name')
                        ->label('Service Name')
                        ->required()
                        ->maxLength(100)
                        ->placeholder('Ganti Oli, Tune Up, AC Service, etc')
                        ->helperText('Nama layanan yang akan ditampilkan'),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->maxLength(500)
                        ->rows(3)
                        ->columnSpanFull()
                        ->placeholder('Deskripsi detail layanan'),

                    Forms\Components\TextInput::make('base_price')
                        ->label('Base Price (Rp)')
                        ->required()
                        ->numeric()
                        ->prefix('Rp')
                        ->minValue(0)
                        ->maxValue(99999999)
                        ->default(0)
                        ->helperText('Harga dasar layanan'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText('Layanan yang tidak aktif tidak akan muncul di booking'),
                ])->columns(2),

            Forms\Components\Section::make('Additional Information')
                ->schema([
                    Forms\Components\Placeholder::make('created_at')
                        ->label('Created At')
                        ->content(fn(?Service $record) => $record ? $record->created_at?->diffForHumans() : '-'),

                    Forms\Components\Placeholder::make('updated_at')
                        ->label('Updated At')
                        ->content(fn(?Service $record) => $record ? $record->updated_at?->diffForHumans() : '-'),
                ])
                ->columns(2)
                ->hidden(fn(?Service $record) => $record === null),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Service Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('base_price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Total Bookings')
                    ->counts('bookings')
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All Services')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),

                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('Price From')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('price_to')
                            ->label('Price To')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['price_from'], fn($q, $price) => $q->where('base_price', '>=', $price))
                            ->when($data['price_to'], fn($q, $price) => $q->where('base_price', '<=', $price));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['price_from'] ?? null) {
                            $indicators[] = 'From: Rp ' . number_format($data['price_from']);
                        }
                        if ($data['price_to'] ?? null) {
                            $indicators[] = 'To: Rp ' . number_format($data['price_to']);
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\BulkAction::make('update_prices')
                        ->label('Update Prices')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('type')
                                ->label('Type')
                                ->options([
                                    'increase' => 'Increase',
                                    'decrease' => 'Decrease',
                                    'set' => 'Set Fixed',
                                ])
                                ->required()
                                ->live(),
                            
                            Forms\Components\TextInput::make('amount')
                                ->label(fn($get) => $get('type') === 'set' ? 'New Price' : 'Amount')
                                ->numeric()
                                ->required()
                                ->prefix('Rp'),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $newPrice = match($data['type']) {
                                    'increase' => $record->base_price + $data['amount'],
                                    'decrease' => max(0, $record->base_price - $data['amount']),
                                    'set' => $data['amount'],
                                };
                                $record->update(['base_price' => $newPrice]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }
}