<?php declare(strict_types=1);

namespace Siteman\Cms;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Siteman\Cms\Blocks\BlockRegistry;
use Siteman\Cms\Blocks\ImageBlock;
use Siteman\Cms\Blocks\MarkdownBlock;
use Siteman\Cms\Enums\FormHook;
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Settings\BlogSettingsForm;
use Siteman\Cms\Settings\GeneralSettings;
use Siteman\Cms\Settings\GeneralSettingsForm;
use Siteman\Cms\Theme\ThemeInterface;

class Siteman
{
    protected array $menuLocations = [];

    protected array $settingsForms = [
        GeneralSettingsForm::class,
        BlogSettingsForm::class,
    ];

    protected array $defaultBlocks = [
        MarkdownBlock::class,
        ImageBlock::class,
    ];

    protected array $formFieldHooks = [];

    protected array $layouts = [];

    public function __construct(protected BlockRegistry $blockRegistry, protected ThemeInterface $theme) {}

    public function boot(): void
    {
        foreach ($this->defaultBlocks as $block) {
            $this->blockRegistry->register(app($block));
        }
        $this->theme->configure($this);
    }

    public function registeredSettingsForms(): array
    {
        return $this->settingsForms;
    }

    public function getGeneralSettings(): GeneralSettings
    {
        return app(GeneralSettings::class);
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
        Blade::component($className::getId(), $className);
        $this->layouts[$className::getId()] = $className;

        return $this;
    }

    public function getLayouts(): array
    {
        return $this->layouts;
    }

    public function registerFormHook(FormHook $hook, \Closure $callback): void
    {
        $this->formFieldHooks[$hook->value] = array_merge($this->formFieldHooks[$hook->value] ?? [], [$callback]);
    }

    public function getFormHooks(?FormHook $hook = null): array
    {
        return $hook ? $this->formFieldHooks[$hook->value] ?? [] : $this->formFieldHooks;
    }
}
