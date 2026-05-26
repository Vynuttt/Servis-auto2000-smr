<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffResource\Pages;
use App\Models\{Role, User};
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\{Auth, Hash};

class StaffResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Access Control';
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Staff';
    protected static ?string $pluralLabel = 'Staff';
    protected static ?string $slug = 'staff';
    protected static ?int $navigationSort = 3;

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
            Forms\Components\Section::make('Account Information')
                ->schema([
                    Forms\Components\FileUpload::make('avatar')
                        ->image()
                        ->directory('avatars')
                        ->visibility('public')
                        ->disk('public')
                        ->imageEditor()
                        ->avatar()
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(100)
                        ->label('Full Name'),

                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(30)
                        ->placeholder('+62 812-xxxx-xxxx'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->inline(false),
                ])->columns(2),

            Forms\Components\Section::make('Password')
                ->description('Leave empty when editing to keep current password.')
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation) => $operation === 'create')
                        ->minLength(8)
                        ->maxLength(255)
                        ->confirmed()
                        ->helperText('Minimum 8 characters'),

                    Forms\Components\TextInput::make('password_confirmation')
                        ->password()
                        ->revealable()
                        ->dehydrated(false)
                        ->requiredWith('password')
                        ->label('Confirm Password'),
                ])->columns(2),

            Forms\Components\Section::make('Role Assignment')
                ->schema([
                    Forms\Components\Select::make('staff_role')
                        ->label('Staff Role')
                        ->options([
                            'admin' => 'Administrator',
                            'owner' => 'Owner',
                        ])
                        ->required()
                        ->default('admin')
                        ->helperText('Select the staff role for this user.')
                        ->live(),
                ]),

            Forms\Components\Section::make('Personal Information')
                ->schema([
                    Forms\Components\DatePicker::make('birthdate')
                        ->native(false)
                        ->maxDate(now()->subYears(17))
                        ->displayFormat('d/m/Y'),

                    Forms\Components\Select::make('gender')
                        ->options([
                            'Male'   => 'Male',
                            'Female' => 'Female',
                        ])
                        ->native(false),

                    Forms\Components\TextInput::make('emergency_contact')
                        ->label('Emergency Contact')
                        ->tel()
                        ->maxLength(30)
                        ->placeholder('+62 812-xxxx-xxxx'),

                    Forms\Components\Textarea::make('address')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])->columns(2)
                ->collapsible(),

            Forms\Components\Section::make('Employment')
                ->schema([
                    Forms\Components\TextInput::make('employee_number')
                        ->label('Employee Number')
                        ->prefix('EMP-')
                        ->maxLength(20)
                        ->unique(ignoreRecord: true)
                        ->helperText('Unique employee identification number.')
                        ->placeholder('STF-001'),
                ])
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 
                        'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=F59E0B&background=FEF3C7'
                    ),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->separator(', ')
                    ->color(fn (string $state) => match ($state) {
                        'Owner' => 'danger',
                        'Administrator' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-m-phone'),

                Tables\Columns\TextColumn::make('employee_number')
                    ->label('Employee #')
                    ->searchable()
                    ->toggleable()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable()
                    ->label('Joined'),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All staff')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),

                Tables\Filters\SelectFilter::make('role')
                    ->label('Staff Role')
                    ->options([
                        'admin' => 'Administrator',
                        'owner' => 'Owner',
                    ])
                    ->query(fn (Builder $query, array $data) => 
                        $data['value']
                            ? $query->whereHas('roles', fn ($q) => $q->where('slug', $data['value']))
                            : $query
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('roles', fn ($q) => $q->whereIn('slug', ['admin', 'owner']));
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit'   => Pages\EditStaff::route('/{record}/edit'),
            'view'   => Pages\ViewStaff::route('/{record}'),
        ];
    }
}   