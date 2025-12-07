<?php declare(strict_types=1);

namespace Siteman\Cms;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use RuntimeException;
use Siteman\Cms\Blocks\BlockRegistry;
use Siteman\Cms\Blocks\ImageBlock;
use Siteman\Cms\Blocks\MarkdownBlock;
use Siteman\Cms\Enums\FormHook;
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Models\Site;
use Siteman\Cms\PageTypes\BlogIndex;
use Siteman\Cms\PageTypes\Page;
use Siteman\Cms\PageTypes\RssFeed;
use Siteman\Cms\PageTypes\TagIndex;
use Siteman\Cms\Settings\GeneralSettings;
use Siteman\Cms\Settings\GeneralSettingsForm;
use Siteman\Cms\Theme\ThemeInterface;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\PermissionRegistrar;

class Siteman
{
    protected ?ThemeInterface $theme = null;

    protected array $menuLocations = [];

    protected array $settingsForms = [
        GeneralSettingsForm::class,
    ];

    protected array $defaultBlocks = [
        MarkdownBlock::class,
        ImageBlock::class,
    ];

    protected array $pageTypes = [
        'page' => Page::class,
        'blog_index' => BlogIndex::class,
        'tag_index' => TagIndex::class,
        'rss_feed' => RssFeed::class,
    ];

    protected array $formFieldHooks = [];

    protected array $layouts = [];

    public function __construct(protected BlockRegistry $blockRegistry, protected GeneralSettings $settings)
    {
        $this->boot();
    }

    public function boot(): void
    {
        $theme = app($this->settings->theme);
        if (!$theme instanceof ThemeInterface) {
            throw new RuntimeException('Theme must implement ThemeInterface');
        }
        $this->theme = $theme;
        foreach ($this->defaultBlocks as $block) {
            $this->registerBlock($block);
        }
        $this->theme->configure($this);
    }

    public function registeredSettingsForms(): array
    {
        return $this->settingsForms;
    }

    public function getGeneralSettings(): GeneralSettings
    {
        return $this->settings;
    }

    public function getMenuItems(string $location): Collection
    {
        $menu = Menu::location($location);

        return $menu ? $menu->menuItems : collect();
    }

    public function blocks(): BlockRegistry
    {
        return $this->blockRegistry;
    }

    public function theme(): ThemeInterface
    {
        return $this->theme;
    }

    public function registerMenuLocation(string $key, string $label): self
    {
        $this->menuLocations[$key] = $label;

        return $this;
    }

    public function getMenuLocations(): array
    {
        return $this->menuLocations;
    }

    public function registerSettingsForm(string $settingsFormClass): self
    {
        $this->settingsForms[] = $settingsFormClass;

        return $this;
    }

    public function registerLayout(string $className): self
    {
        if (!array_key_exists($className::getId(), Blade::getClassComponentAliases())) {
            throw new RuntimeException('Layout must be registered as Blade component');
        }
        $this->layouts[$className::getId()] = $className;

        return $this;
    }

    public function registerBlock(string $className): self
    {
        $this->blockRegistry->register(app($className));

        return $this;
    }

    public function getLayouts(): array
    {
        return $this->layouts;
    }

    public function registerFormHook(FormHook $hook, Closure $callback): void
    {
        $this->formFieldHooks[$hook->value] = array_merge($this->formFieldHooks[$hook->value] ?? [], [$callback]);
    }

    public function getFormHooks(?FormHook $hook = null): array
    {
        return $hook ? $this->formFieldHooks[$hook->value] ?? [] : $this->formFieldHooks;
    }

    public function getPageTypes(): array
    {
        return $this->pageTypes;
    }

    public function createSuperAdminRole(): Role
    {
        return $this->createRole('super-admin');
    }

    public function createRole(string $name): Role
    {
        return $this->getRoleModel()::firstOrCreate([
            'name' => $name,
            'guard_name' => 'web',
        ]);
    }

    public function getPermissionModel(): string
    {
        return app(PermissionRegistrar::class)->getPermissionClass();
    }

    public function getRoleModel(): string
    {
        return app(PermissionRegistrar::class)->getRoleClass();
    }

    public function isSuperAdmin(Role $role): bool
    {
        return $role->name === 'super-admin';
    }

    public function getResourceInfoForPermissions(): Collection
    {
        return collect(Filament::getResources())
            ->map(fn ($resource) => [
                'identifier' => $this->getDefaultPermissionIdentifier($resource),
                'model' => $resource::getModel(),
                'model_name' => Str::of($resource::getModel())->afterLast('\\')->lower()->toString(),
                'resource' => $resource,
            ]);
    }

    public function getPermissionsFor(string $model): array
    {
        return method_exists($model, 'getPermissions')
            ? $model::getPermissions()
            : [
                'view_any',
                'view',
                'create',
                'update',
                'delete',
            ];
    }

    public function getLoginUrl(): string
    {
        return route('filament.admin.auth.login');
    }

    public function getCurrentSite(): ?Site
    {
        if ($site = Context::get('current_site')) {
            return $site;
        }
        $site = Filament::getTenant();
        if ($site instanceof Site) {
            $this->setCurrentSite($site);

            return $site;
        }
        if ($site = Site::where('domain', request()->getHost())->first()) {
            $this->setCurrentSite($site);

            return $site;
        }

        return null;
    }

    public function setCurrentSite(Site|int $site): void
    {
        if (is_int($site)) {
            $site = Site::findOrFail($site);
        }
        Context::add('current_site', $site);
    }

    protected function getDefaultPermissionIdentifier(string $resource): string
    {
        return Str::of($resource)
            ->afterLast('Resources\\')
            ->beforeLast('Resource')
            ->replace('\\', '')
            ->snake()
            ->replace('_', '::')
            ->toString();
    }
}
