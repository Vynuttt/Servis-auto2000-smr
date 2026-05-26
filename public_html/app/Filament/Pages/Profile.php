<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Profile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $title           = 'My Profile';
    protected static ?string $slug            = 'profile';
    protected static string  $view            = 'filament.pages.profile';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check();
    }

    public function mount(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        $this->form->fill([
            'name'              => $user->name,
            'email'             => $user->email,
            'phone'             => $user->phone,
            'avatar'            => $user->avatar,
            'address'           => $user->address,
            'birthdate'         => $user->birthdate,
            'gender'            => $user->gender,
            'emergency_contact' => $user->emergency_contact,
            'employee_number'   => $user->employee_number,
        ]);
    }

    public function form(Form $form): Form
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        return $form
            ->schema([
                Forms\Components\Section::make('Account')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->disk('avatars')          // 🔥 pakai disk baru
                            ->directory('')            // simpan langsung di /uploads/avatars
                            ->visibility('public')     // pastikan bisa diakses publik
                            ->image()
                            ->imageEditor()
                            ->avatar()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(30),
                    ])->columns(2),

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
                    ])->columns(2),

                Forms\Components\Section::make('Employment')
                    ->schema([
                        Forms\Components\TextInput::make('employee_number')
                            ->label('Employee Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Only Admin/Owner can assign this number.'),
                    ])
                    ->visible(fn () => filled($user->employee_number)),

                Forms\Components\Section::make('Change Password')
                    ->description('Leave password fields empty if you don\'t want to change your password.')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(
                                fn ($state) => filled($state) ? Hash::make($state) : null
                            )
                            ->dehydrated(fn ($state) => filled($state))
                            ->confirmed()
                            ->minLength(8)
                            ->maxLength(255)
                            ->helperText('Minimum 8 characters'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->requiredWith('password'),
                    ])->columns(2),
            ])
            ->statePath('data')
            ->model($user);
    }

    public function save(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        $data = $this->form->getState();

        // Field yang tidak boleh di-update manual
        unset($data['employee_number'], $data['email'], $data['current_password']);

        // Update user
        $user->update($data);

        // Notifikasi sukses
        Notification::make()
            ->title('Profile Updated Successfully')
            ->success()
            ->send();

        // Refresh model & form
        $user->refresh();

        $this->form->fill([
            'name'              => $user->name,
            'email'             => $user->email,
            'phone'             => $user->phone,
            'avatar'            => $user->avatar,
            'address'           => $user->address,
            'birthdate'         => $user->birthdate,
            'gender'            => $user->gender,
            'emergency_contact' => $user->emergency_contact,
            'employee_number'   => $user->employee_number,
        ]);
    }
}
