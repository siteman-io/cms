<?php declare(strict_types=1);

namespace Siteman\Cms\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Siteman\Cms\Facades\Siteman;

class SettingsPage extends Page
{
    use HasPageShield;

    protected static string $view = 'siteman::pages.settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    public function getSettings()
    {
        return collect(Siteman::registeredSettings());
    }

    protected function fillForm(): void
    {
        foreach ($this->getSettings() as $setting) {
            $formName = $setting::group().'SettingsForm';
            $this->$formName->fill(app($setting)->toArray());
        }
    }

    public function save($group): void
    {
        $settingName = $this->getSettings()->first(fn (string $setting) => $this->getGroupName($setting) === $group, '');
        $instance = app($settingName);
        $formName = $this->getGroupName($settingName).'SettingsForm';
        $instance->fill($this->$formName->getState());
        $instance->save();
        Notification::make()
            ->success()
            ->title(__('siteman::pages/settings.notifications.saved', ['group' => Str::headline($this->getGroupName($settingName))]))
            ->send();
    }

    protected function getForms(): array
    {
        return $this->getSettings()
            ->mapWithKeys(fn ($setting) => [$setting::group().'SettingsForm' => $this->makeForm()
                ->schema(app($setting)->form())
                ->statePath('data.'.$setting::group())])
            ->toArray();
    }

    public static function getNavigationGroup(): ?string
    {
        return parent::getNavigationGroup() ?? __('siteman::pages/settings.navigation-group');
    }

    public static function getNavigationIcon(): ?string
    {
        return parent::getNavigationIcon() ?? __('siteman::pages/settings.navigation-icon');
    }

    public static function getNavigationLabel(): string
    {
        return __('siteman::pages/settings.navigation-label');
    }

    public function getHeading(): string|Htmlable
    {
        return __('siteman::pages/settings.heading');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('siteman::pages/settings.subheading');
    }

    protected function getGroupName(string $class): string
    {
        return method_exists($class, 'group') ? $class::group() : class_basename($class);
    }
}
