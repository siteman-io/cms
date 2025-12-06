<?php declare(strict_types=1);

namespace Siteman\Cms;

use Filament\Auth\Pages\EditProfile;
use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Pages\SettingsPage;
use Siteman\Cms\Pages\SiteHealthPage;
use Siteman\Cms\Resources\Menus\MenuResource;
use Siteman\Cms\Resources\Pages\PageResource;
use Siteman\Cms\Resources\Roles\RoleResource;
use Siteman\Cms\Resources\Users\UserResource;
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
        $resources = [PageResource::class, MenuResource::class, UserResource::class, RoleResource::class];
        $panel->resources($resources);

        $panel->pages([
            SettingsPage::class,
            SiteHealthPage::class,
        ]);

        $panel->profile(EditProfile::class, false);
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
        $panel->renderHook(
            'panels::global-search.before',
            fn () => view('siteman::snippets.homepage-link', ['page' => Page::getHomePage()]),
        );

        $panel->sidebarFullyCollapsibleOnDesktop()
            ->sidebarWidth('14rem')
            ->maxContentWidth(Width::Full);
    }

    public function boot(Panel $panel): void
    {
        $theme = app(ThemeInterface::class);
        if (method_exists($theme, 'configurePanel')) {
            $theme->configurePanel($panel);
        }
    }
}
