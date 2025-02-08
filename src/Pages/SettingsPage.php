<?php declare(strict_types=1);

namespace Siteman\Cms\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Settings\SettingsFormInterface;
use Spatie\LaravelSettings\Settings;

class SettingsPage extends Page
{
    use HasPageShield;

    protected static string $view = 'siteman::pages.settings-page';

    public ?array $data = [];

    protected ?Collection $settingForms = null;

    public function mount(): void
    {
        $this->fillForm();
    }

    /**
     * @return Collection<string, SettingsFormInterface>
     */
    public function getSettingForms(): Collection
    {
        if ($this->settingForms !== null) {
            return $this->settingForms;
        }
        $this->settingForms = collect(Siteman::registeredSettings())->mapWithKeys(function (string $formClass) {
            /** @var SettingsFormInterface $form */
            $form = app($formClass);

            return [$this->getGroupName($form::getSettingsClass()) => $form];
        });

        return $this->settingForms;
    }

    protected function fillForm(): void
    {
        foreach ($this->getSettingForms() as $group => $form) {

            $formName = $group.'SettingsForm';
            $this->$formName->fill(app($form::getSettingsClass())->toArray());
        }
    }

    public function save($group): void
    {
        $settingsForm = $this->getSettingForms()[$group];
        $formName = $group.'SettingsForm';
        $data = $this->$formName->getState();
        if (method_exists($settingsForm, 'mutateBeforeSaving')) {
            $data = $settingsForm->mutateBeforeSaving($data);
        }
        /** @var Settings $settings */
        $settings = app($settingsForm->getSettingsClass());
        $settings->fill($data);
        $settings->save();
        Notification::make()
            ->success()
            ->title(__('siteman::pages/settings.notifications.saved', ['group' => Str::headline($group)]))
            ->send();
    }

    protected function getForms(): array
    {
        return $this->getSettingForms()
            ->mapWithKeys(fn (SettingsFormInterface $form, string $group) => [$group.'SettingsForm' => $this->makeForm()
                ->schema($form->schema())
                ->statePath('data.'.$group)])
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
