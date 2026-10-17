<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => Blade::render(<<<'HTML'
<style>
@font-face{font-family:'Vazirmatn';src:url('/fonts/Vazirmatn-Regular.woff2') format('woff2');font-weight:400;font-display:swap}
@font-face{font-family:'Vazirmatn';src:url('/fonts/Vazirmatn-Medium.woff2') format('woff2');font-weight:500;font-display:swap}
@font-face{font-family:'Vazirmatn';src:url('/fonts/Vazirmatn-Bold.woff2') format('woff2');font-weight:700;font-display:swap}
@font-face{font-family:'Kalameh';src:url('/fonts/KalamehWeb-Regular.woff2') format('woff2');font-weight:400;font-display:swap}
@font-face{font-family:'Kalameh';src:url('/fonts/KalamehWeb-Medium.woff2') format('woff2');font-weight:500;font-display:swap}
@font-face{font-family:'Kalameh';src:url('/fonts/KalamehWeb-Bold.woff2') format('woff2');font-weight:700;font-display:swap}
@font-face{font-family:'Dana';src:url('/fonts/Dana-Regular.woff2') format('woff2');font-weight:400;font-display:swap}
@font-face{font-family:'Dana';src:url('/fonts/Dana-Medium.woff2') format('woff2');font-weight:500;font-display:swap}
@font-face{font-family:'Dana';src:url('/fonts/Dana-Bold.woff2') format('woff2');font-weight:700;font-display:swap}
:root{--font-sans:'Dana',Tahoma,ui-sans-serif,system-ui,sans-serif;--font-display:'Kalameh','Dana',Tahoma,sans-serif;--font-toman:'Vazirmatn',Tahoma,sans-serif}
html,body,.fi-body{font-family:var(--font-sans) !important}
.fi-logo,.font-display{font-family:var(--font-display) !important}
.fi-page h1,.fi-page h2,.fi-header-heading,.fi-section-header-heading,.fi-modal-heading{font-family:var(--font-display) !important}
.font-toman{font-family:var(--font-toman) !important;font-variant-numeric:tabular-nums}
</style>
HTML),
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
