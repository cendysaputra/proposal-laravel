<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(\App\Filament\Pages\Auth\EditProfile::class)
            ->brandName('PT Digital Citra Kreatif')
            ->favicon(asset('fav_icon.ico'))
            ->colors([
                'primary' => '#004258',
            ])
            ->plugins([
                //
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
               //  Widgets\FilamentInfoWidget::class,
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
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => '<style>
                    /* Sidebar border */
                    .fi-sidebar {
                        border-right: 1px solid rgb(229 231 235) !important;
                    }
                    .dark .fi-sidebar {
                        border-right: 1px solid rgb(55 65 81) !important;
                    }

                    /* Brand name color - multiple selectors to ensure it works */
                    .fi-sidebar-header .fi-sidebar-brand,
                    .fi-sidebar .fi-logo,
                    .fi-sidebar nav > div > a,
                    aside nav > div > a {
                        color: rgb(0 66 88) !important;
                        font-weight: 600 !important;
                    }

                    .dark .fi-sidebar-header .fi-sidebar-brand,
                    .dark .fi-sidebar .fi-logo,
                    .dark .fi-sidebar nav > div > a,
                    .dark aside nav > div > a {
                        color: rgb(40 190 193) !important;
                    }
                </style>'
            );
    }
}
