<?php declare(strict_types=1);

namespace Siteman\Cms\Pages;

use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Pages\Concerns\IsProtectedPage;
use Siteman\Cms\Settings\SettingsFormInterface;
use Spatie\LaravelSettings\Settings;

class SettingsPage extends Page
{
    use InteractsWithForms;
    use IsProtectedPage;

    public ?array $data = [];

    public ?string $activeTab = null;

    protected ?Collection $settingForms = null;

    public function mount(): void
    {
        $this->activeTab = request()->query('group', 'general');
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
        $this->settingForms = collect(Siteman::registeredSettingsForms())->mapWithKeys(function (string $formClass) {
            /** @var SettingsFormInterface $form */
            $form = app($formClass);

            return [$this->getGroupName($form::getSettingsClass()) => $form];
        });

        return $this->settingForms;
    }

    protected function fillForm(): void
    {
        foreach ($this->getSettingForms() as $group => $form) {
            $this->getGroupForm($group)->fill(app($form::getSettingsClass())->toArray());
        }
    }

    public function save($group): void
    {
        $settingsForm = $this->getSettingForms()[$group];
        $state = $this->getGroupForm($group)->getState();
        if (method_exists($settingsForm, 'mutateDehydratedState')) {
            $state = $settingsForm->mutateDehydratedState($state);
        }
        /** @var Settings $settings */
        $settings = app($settingsForm->getSettingsClass());
        $settings->fill($state);
        $settings->save();
        Notification::make()
            ->success()
            ->title(__('siteman::settings.notifications.saved', ['group' => Str::headline($group)]))
            ->send();
    }

    protected function getForms(): array
    {
        return $this->getSettingForms()
            ->mapWithKeys(
                fn (SettingsFormInterface $form, string $group) => [
                    $this->getFormName($group) => $this->makeSchema()
                        ->components($form->schema())
                        ->statePath('data.'.$group),
                ])
            ->toArray();
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components(
            Tabs::make()
                ->vertical()
                ->tabs(
                    $this->getSettingForms()
                        ->map(fn (SettingsFormInterface $form, string $group) => Tab::make($group)
                            ->icon($form->icon())
                            ->schema([
                                Form::make($form->schema())
                                    ->statePath('data.'.$group)
                                    ->footer(Action::make('submit')->action(fn () => $this->save($group))),
                            ]))
                        ->values()
                        ->all(),
                )
                ->persistTabInQueryString('group')
        );
    }

    public static function getNavigationGroup(): ?string
    {
        return parent::getNavigationGroup() ?? __('siteman::settings.navigation.group');
    }

    public static function getNavigationIcon(): ?string
    {
        return parent::getNavigationIcon() ?? __('siteman::settings.navigation.icon');
    }

    public static function getNavigationLabel(): string
    {
        return __('siteman::settings.label');
    }

    public function getHeading(): string|Htmlable
    {
        return __('siteman::settings.label');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('siteman::settings.subheading');
    }

    protected function getGroupName(string $class): string
    {
        return method_exists($class, 'group') ? $class::group() : class_basename($class);
    }

    protected function getFormName(string $group): string
    {
        return $group.'SettingsForm';
    }

    protected function getGroupForm(string $group): ?Schema
    {
        $formName = $this->getFormName($group);

        return $this->$formName;
    }
}
