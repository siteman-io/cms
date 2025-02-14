<?php

declare(strict_types=1);

namespace Siteman\Cms;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Auth\EditProfile;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Pboivin\FilamentPeek\FilamentPeekPlugin;
use Siteman\Cms\Pages\SettingsPage;
use Siteman\Cms\Pages\SiteHealthPage;
use Siteman\Cms\Resources\MenuResource;
use Siteman\Cms\Resources\PageResource;
use Siteman\Cms\Resources\PostResource;
use Siteman\Cms\Resources\RoleResource;
use Siteman\Cms\Resources\UserResource;
use Siteman\Cms\Settings\BlogSettings;
use Siteman\Cms\Theme\ThemeInterface;

class SitemanPlugin implements Plugin
{
    public function __construct(protected ThemeInterface $theme) {}

    public static function make(): self
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'siteman';
    }

    public function register(Panel $panel): void
    {
        $panel->navigationGroups([
            NavigationGroup::make('Content')->collapsible(false),
            NavigationGroup::make('Admin')->collapsible()->collapsed(),
        ]);
        $resources = [PageResource::class, UserResource::class, RoleResource::class, MenuResource::class];
        if (BlogSettings::isEnabled()) {
            $resources[] = PostResource::class;
            $panel->widgets([PostResource\Widgets\LatestPostWidget::class]);
        }
        $panel->resources($resources);
        $panel->pages([
            SettingsPage::class,
            SiteHealthPage::class,
        ]);

        $panel->profile(EditProfile::class, false);
        $panel->plugin(FilamentShieldPlugin::make());
        $panel->plugin(FilamentPeekPlugin::make());
        $panel->renderHook(
            PanelsRenderHook::TOPBAR_START,
            fn () => Blade::render(sprintf('<x-filament::link href="/">%s</x-filament::link>', __('siteman::general.go-to-site-link'))),
        );

        $panel->renderHook('panels::global-search.before', function () {
            $env = app()->environment();

            return view('siteman::snippets.environment-indicator', [
                'color' => match ($env) {
                    'production' => Color::Red,
                    'staging' => Color::Orange,
                    'development' => Color::Blue,
                    default => Color::Pink,
                },
                'environment' => $env,
            ]);
        });

        if (method_exists($this->theme, 'configurePanel')) {
            $this->theme->configurePanel($panel);
        }

        $panel->sidebarFullyCollapsibleOnDesktop()->sidebarWidth('14rem');
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
