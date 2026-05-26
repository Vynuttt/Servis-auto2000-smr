<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


class CustomerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Access Control';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Customers';
    protected static ?string $slug = 'customers';

//    public static function shouldRegisterNavigation(): bool
//         {
//             $user = Auth::user();
//             if (!$user) return false;
            
//             /** @var \App\Models\User $user */
//             return $user->isAdminOrOwner();
//         }

public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->required()->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()->required()->maxLength(255)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('phone')
                    ->tel()->maxLength(30),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')->default(true),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->label('Password (leave blank to keep)'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->searchable()->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /** Inti filter: hanya ambil user yg punya role 'customer' */
    public static function getEloquentQuery(): Builder
    {
        return static::getModel()::query()
            ->whereHas('roles', fn ($q) => $q->where('slug', 'customer'));
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
