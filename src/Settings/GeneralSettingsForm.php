<?php declare(strict_types=1);

namespace Siteman\Cms\Settings;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Siteman\Cms\Theme\ThemeRegistry;

class GeneralSettingsForm implements SettingsFormInterface
{
    public static function getSettingsClass(): string
    {
        return GeneralSettings::class;
    }

    public function icon(): string
    {
        return 'heroicon-o-globe-alt';
    }

    public function schema(): array
    {
        return [
            TextInput::make('site_name')
                ->label(__('siteman::settings.general.fields.site_name.label'))
                ->helperText(__('siteman::settings.general.fields.site_name.helper-text'))
                ->required(),
            Textarea::make('description')
                ->label(__('siteman::settings.general.fields.description.label'))
                ->helperText(__('siteman::settings.general.fields.description.helper-text'))
                ->rows(2),
            Select::make('theme')
                ->label(__('siteman::settings.general.fields.theme.label'))
                ->helperText(__('siteman::settings.general.fields.theme.helper-text'))
                ->options(fn () => collect(app(ThemeRegistry::class)->getThemes())->mapWithKeys(fn ($theme) => [$theme => $theme::getName()]))
                ->required(),
        ];
    }
}
