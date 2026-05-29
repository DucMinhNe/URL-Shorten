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
            ->brandName('LinkPay Admin')
            ->favicon(asset('favicon.ico'))
            ->darkMode(false)
            ->colors([
                'primary' => [
                    50  => '#e7e7ff',
                    100 => '#d2d3ff',
                    200 => '#a5a7ff',
                    300 => '#878aff',
                    400 => '#7679ff',
                    500 => '#696cff',
                    600 => '#5f61e6',
                    700 => '#4a4dcc',
                    800 => '#3f4199',
                    900 => '#2a2b66',
                    950 => '#151633',
                ],
                'gray' => Color::Slate,
                'danger' => Color::Red,
                'success' => [
                    50  => '#e8fadf',
                    500 => '#71dd37',
                    600 => '#5cb928',
                ],
                'warning' => [
                    50  => '#fff2d6',
                    500 => '#ffab00',
                    600 => '#cc8900',
                ],
                'info' => [
                    50  => '#d7f5fc',
                    500 => '#03c3ec',
                    600 => '#029cbd',
                ],
            ])
            ->font('Public Sans')
            ->sidebarCollapsibleOnDesktop()
            ->breadcrumbs(true)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\ClicksChart::class,
                \App\Filament\Widgets\PendingPayouts::class,
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
            ]);
    }
}
