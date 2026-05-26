<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// TAMBAHAN BARU
use Filament\Navigation\MenuItem;
use App\Filament\Pages\Profile;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            
            // --- WARNA TELAH DIUBAH DI SINI ---
            ->colors([
                'primary' => Color::Red,  // Diubah dari Amber ke Red
                'info'    => Color::Blue, // Ditambahkan untuk warna Astra Blue
            ])
            // --- SELESAI PERUBAHAN WARNA ---
            
            // --- INI PERBAIKANNYA ---
            // Gunakan path URL yang bisa diakses web, bukan path hard drive D:
            ->favicon(asset('images/logoAuto2000.png'))
            ->brandName('WebsiteAuto2000')
            ->brandLogo(asset('images/logoAuto2000.png'))
            // --- SELESAI PERBAIKAN ---

            ->brandLogoHeight('2rem')
            
            // ✅ DISCOVER RESOURCES (penting!)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            
            // ✅ DISCOVER PAGES (penting!)
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            
            // Dashboard default
            ->pages([
                Pages\Dashboard::class,
                Profile::class, // <-- TAMBAHAN BARU
                
            ])
            
            // ✅ DISCOVER WIDGETS (penting!)
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            
            // Default widgets
            ->widgets([
                //Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class, // bisa dikomentari jika tidak perlu
            ])

            // TAMBAHAN BARU
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('My Profile')
                    ->icon('heroicon-m-user-circle')
                    ->url(fn() => Profile::getUrl()),
            ])

            ->viteTheme('resources/css/filament/admin/theme.css')

            
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            
            // Optional: Dark mode, SPA mode, dll
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full');
            
    }
}