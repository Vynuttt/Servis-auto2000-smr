<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;   // ⬅️ yang benar: Resource (tunggal)
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


class RoleResource extends Resource   // ⬅️ nama kelas = nama file
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationGroup = 'Access Control';
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Roles';

    // public static function shouldRegisterNavigation(): bool
    // {
    //     $user = Auth::user();
    //     if (!$user) return false;
        
    //     /** @var \App\Models\User $user */
    //     return $user->isAdminOrOwner();
    // }

    public static function shouldRegisterNavigation(): bool
    {   
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(50),

            Forms\Components\TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(50),

            Forms\Components\Textarea::make('description')
                ->rows(2),
        ])->columns(2);
    }
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->withCount('users');
}
    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('slug')
                ->label('Slug')
                ->badge()
                ->copyable(),

            Tables\Columns\TextColumn::make('users_count')
                ->label('Users')
                ->counts('users')
                ->sortable(),

            Tables\Columns\TextColumn::make('updated_at')
                ->label('Updated')
                ->since(),
        ])
        ->filters([])
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
            'index'  => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit'   => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
