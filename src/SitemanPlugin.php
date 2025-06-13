<?php declare(strict_types=1);

namespace Siteman\Cms;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Auth\EditProfile;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Pboivin\FilamentPeek\FilamentPeekPlugin;
use Siteman\Cms\Pages\SettingsPage;
use Siteman\Cms\Pages\SiteHealthPage;
use Siteman\Cms\Resources\MenuResource;
use Siteman\Cms\Resources\PageResource;
use Siteman\Cms\Resources\UserResource;
use Siteman\Cms\Search\SitemanSearchProvider;
use Siteman\Cms\Theme\ThemeInterface;

class SitemanPlugin implements Plugin
{
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
        $resources = [PageResource::class, MenuResource::class, UserResource::class];
        $panel->resources($resources);

        $panel->pages([
            SettingsPage::class,
            SiteHealthPage::class,
        ]);

        $panel->profile(EditProfile::class, false);
        $panel->plugin(FilamentPeekPlugin::make());
        $panel->renderHook(
            PanelsRenderHook::TOPBAR_START,
            fn () => Blade::render(sprintf('<x-filament::link href="/">%s</x-filament::link>', __('siteman::dashboard.go-to-site'))),
        );
        $panel->globalSearch(SitemanSearchProvider::class);

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

        $panel->sidebarFullyCollapsibleOnDesktop()
            ->sidebarWidth('14rem')
            ->maxContentWidth(MaxWidth::Full);
    }

    public function boot(Panel $panel): void
    {
        $theme = app(ThemeInterface::class);
        if (method_exists($theme, 'configurePanel')) {
            $theme->configurePanel($panel);
        }
    }
}
