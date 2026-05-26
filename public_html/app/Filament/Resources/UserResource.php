<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;   // <- benar: Resource (tunggal)
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource   // <- nama kelas = nama file
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Access Control';
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Users';


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

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Account')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()->maxLength(100),

                    Forms\Components\TextInput::make('email')
                        ->email()->required()->unique(ignoreRecord: true),

                    Forms\Components\TextInput::make('phone')
                        ->tel()->maxLength(30),

                    Forms\Components\FileUpload::make('avatar')
                        ->image()->directory('avatars')->imageEditor(),

                    Forms\Components\Toggle::make('is_active')->default(true),
                ])->columns(2),

            Forms\Components\Section::make('Password')
                ->schema([
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation) => $operation === 'create')
                        ->same('password_confirmation'),

                    Forms\Components\TextInput::make('password_confirmation')
                        ->password()->dehydrated(false),
                ])->columns(2),

            Forms\Components\Section::make('Roles')
                ->schema([
                    Forms\Components\Select::make('roles')
                        ->label('Assign Roles')
                        ->relationship('roles', 'name')   // pivot user_roles
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->options(Role::query()->pluck('name', 'id')),
                ]),

                Forms\Components\Section::make('Employment')
                ->schema([
                    Forms\Components\TextInput::make('employee_number')
                        ->label('Employee Number')
                        ->prefix('EMP-') // opsional: hanya visual
                        ->maxLength(20)
                        ->unique(ignoreRecord: true) // aman untuk edit
                        ->helperText('Only Admin/Owner can set this value.')
                        ->disabled(function () {
                            $u = Auth::user();
                            if (!$u) return true;
                            
                            /** @var \App\Models\User $u */
                            return !$u->isAdminOrOwner();
                        })
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')->circular(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->copyable()->sortable(),
                Tables\Columns\TextColumn::make('roles.name')   // no BadgeColumn (deprecated)
                    ->label('Roles')->separator(', ')->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('created_at')->since()->label('Created'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options(Role::pluck('name', 'slug'))
                    ->query(fn ($query, $data) =>
                        $data['value']
                            ? $query->whereHas('roles', fn ($q) => $q->where('slug', $data['value']))
                            : $query
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
