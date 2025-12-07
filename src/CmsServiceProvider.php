<?php declare(strict_types=1);

namespace Siteman\Cms;

use Bambamboole\ExtendedFaker\ExtendedFaker;
use Faker\Generator;
use Filament\Forms\Components\Field;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use RalphJSmit\Laravel\SEO\SEOManager;
use RalphJSmit\Laravel\SEO\TagManager;
use Siteman\Cms\Blocks\BlockRegistry;
use Siteman\Cms\Commands\CreateAdminCommand;
use Siteman\Cms\Commands\InstallCommand;
use Siteman\Cms\Commands\MakeBlockCommand;
use Siteman\Cms\Commands\MakeSettingsCommand;
use Siteman\Cms\Commands\MakeSiteCommand;
use Siteman\Cms\Commands\MakeThemeCommand;
use Siteman\Cms\Commands\PublishCommand;
use Siteman\Cms\Commands\UpdateCommand;
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Models\Tag;
use Siteman\Cms\Policies\MenuPolicy;
use Siteman\Cms\Policies\PagePolicy;
use Siteman\Cms\Policies\RolePolicy;
use Siteman\Cms\Policies\UserPolicy;
use Siteman\Cms\Resources\Menus\Livewire\CreateCustomLink;
use Siteman\Cms\Resources\Menus\Livewire\CreateCustomText;
use Siteman\Cms\Resources\Menus\Livewire\CreatePageLink;
use Siteman\Cms\Resources\Menus\Livewire\MenuItems;
use Siteman\Cms\Resources\Pages\Livewire\PageDetails;
use Siteman\Cms\Resources\Pages\Livewire\PageTree;
use Siteman\Cms\Resources\Pages\Pages\EditPage;
use Siteman\Cms\Resources\Pages\Pages\ViewPage;
use Siteman\Cms\Theme\BaseLayout;
use Siteman\Cms\Theme\ThemeInterface;
use Siteman\Cms\Theme\ThemeRegistry;
use Siteman\Cms\Widgets\HealthCheckResultWidget;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Facades\Health;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Permission\Models\Role;
use Torchlight\Middleware\RenderTorchlight;

class CmsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('siteman')
            ->hasConfigFile()
            ->hasViews('siteman')
            ->hasRoute('web')
            ->hasMigrations([
                'create_sites_table',
                'create_menus_table',
                'create_pages_table',
                '../settings/create_general_settings',
            ])
            ->hasTranslations()
            ->hasCommands([
                InstallCommand::class,
                UpdateCommand::class,
                PublishCommand::class,
                MakeThemeCommand::class,
                MakeBlockCommand::class,
                MakeSettingsCommand::class,
                MakeSiteCommand::class,
                CreateAdminCommand::class,
            ]);

    }

    public function registeringPackage()
    {
        $this->app->singleton(BlockRegistry::class);
        $this->app->singleton(ThemeRegistry::class, fn () => new ThemeRegistry(
            new Filesystem,
            $this->app->basePath('vendor'),
            config('siteman.themes', []),
        ));
        $this->app->singleton(ThemeInterface::class, fn () => \Siteman\Cms\Facades\Siteman::theme());
        $this->app->singleton(Siteman::class);
        $this->app->singleton(SitemanPlugin::class);
    }

    public function bootingPackage(): void
    {
        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command(RunHealthChecksCommand::class)->everyMinute();
        });

        $config = $this->app->make(Repository::class);

        $config->set('settings.migrations_paths', array_merge(
            $config->get('settings.migrations_paths', []),
            [$this->getPackageBaseDir().'/database/settings'],
        ));

        $config->set('tags.tag_model', Tag::class);
        $config->set('permission.teams', true);
        $config->set('permission.models.role', Models\Role::class);
        $config->set('permission.column_names.team_foreign_key', 'site_id');
        $config->set('filament-shield.shield_resource.show_model_path', false);
        $config->set('filament-shield.permission_prefixes.resource', [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'delete_any',
        ]);

        $this->app->afterResolving(SEOManager::class, function (SEOManager $seoManager) {
            $siteName = \Siteman\Cms\Facades\Siteman::getGeneralSettings()->site_name;
            config()->set('seo.title.homepage_title', $siteName);
            config()->set('seo.title.suffix', ' | '.$siteName);

            return $seoManager;
        });
        $this->app->afterResolving(TagManager::class, function (TagManager $tagManager) {
            $siteName = \Siteman\Cms\Facades\Siteman::getGeneralSettings()->site_name;
            config()->set('seo.title.homepage_title', $siteName);
            config()->set('seo.title.suffix', ' | '.$siteName);

            return $tagManager;
        });

        if (config('torchlight.token') !== null) {
            $this->app->afterResolving(
                Kernel::class,
                fn (Kernel $kernel) => $kernel->prependMiddleware(RenderTorchlight::class),
            );
        }
        $this->app->afterResolving(Generator::class, function (Generator $generator) {
            ExtendedFaker::extend($generator);

            return $generator;
        });
    }

    public function packageBooted(): void
    {
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName(),
        );

        Blade::component('base-layout', BaseLayout::class);

        Livewire::component('health-check-result', HealthCheckResultWidget::class);
        Livewire::component('menu-items', MenuItems::class);
        Livewire::component('create-custom-link', CreateCustomLink::class);
        Livewire::component('create-custom-text', CreateCustomText::class);
        Livewire::component('create-page-link', CreatePageLink::class);
        Livewire::component('page-tree', PageTree::class);
        Livewire::component('page-details', PageDetails::class);
        Livewire::component('view-page', ViewPage::class);
        Livewire::component('edit-page', EditPage::class);

        Health::checks([
            EnvironmentCheck::new(),
            OptimizedAppCheck::new(),
            ScheduleCheck::new(),
            CacheCheck::new(),
        ]);

        Gate::policy(config('siteman.models.user'), UserPolicy::class);
        Gate::policy(Menu::class, MenuPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Page::class, PagePolicy::class);

        Gate::before(function (User $user) {
            if (method_exists($user, 'hasRole')) {
                return $user->hasRole('super-admin') ? true : null;
            }

            return null;
        });

        Field::macro('asPageMetaField', function () {
            /** @var Field $this */
            return $this->statePath('meta.'.$this->getName());
        });
    }

    protected function getAssetPackageName(): ?string
    {
        return 'siteman';
    }

    protected function getAssets(): array
    {
        return [
            AlpineComponent::make('menu', __DIR__.'/../resources/dist/js/components/menu.js'),
            Css::make('components', __DIR__.'/../resources/dist/css/components.css'),
            Css::make('admin-bar', __DIR__.'/../resources/dist/css/admin-bar.css')->loadedOnRequest(),
        ];
    }
}
