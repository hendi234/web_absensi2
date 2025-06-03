<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Filament\Pages\Auth\LoginCustom;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class AbsensiPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('absensi')
            ->path('absensi')
            ->login(action: LoginCustom::class)
            // ->registration()
            ->colors([
                'primary' => Color::Emerald,
                'gray' => Color::Gray,
            ])
            ->sidebarFullyCollapsibleOnDesktop()
            ->brandLogo(fn() => auth()->check() 
                ? asset('images/iconhome.png')  // Logo setelah login (Dashboard)
                : asset('images/icon.png')  // Logo sebelum login (Halaman Login)
            )
            ->brandLogoHeight(fn() => auth()->check() ? '3.9rem' : '5.5rem')
            ->darkModeBrandLogo(fn() => auth()->check() 
                ? asset('images/darkiconhome.png')  // Logo setelah login (Dashboard)
                : asset('images/icon.png')  // Logo sebelum login (Halaman Login)
            )
            ->favicon(asset('images/artur.png'))
            ->font('Roboto')
            ->brandName('PT arthur teknik indoprima')
            //  ->darkMode(false)
            // ->pages([
            //     Pages\Dashboard::class,
            // ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])    
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
            ->databaseNotifications(fn () => auth()->user()->isAdmin())
            ->plugins([   
                FilamentEditProfilePlugin::make()
                    ->customProfileComponents([
                        \App\Livewire\CustomProfileComponent::class,
                    ])
                    ->shouldShowBrowserSessionsForm(false)
                    ->shouldShowDeleteAccountForm(false)
                    ->slug('my-profile')
                    ->setTitle('Profile')
                    ->setNavigationLabel(fn() => auth()->user()->name)
                    ->setIcon('heroicon-o-user-circle')
                    ->shouldShowAvatarForm(
                        directory: 'karyawan',
                    ),
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => auth()->user()->name)
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle')
            ]);
    }
}
