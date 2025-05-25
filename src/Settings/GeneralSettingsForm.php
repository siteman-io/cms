<?php declare(strict_types=1);

namespace Siteman\Cms\Settings;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
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
        $themes = app(ThemeRegistry::class)->getThemes();
        $themeOptions = collect($themes)
            ->mapWithKeys(function ($theme) {
                $value = method_exists($theme, 'getName')
                    ? $theme::getName()
                    : Str::afterLast($theme, '\\');

                return [$theme => $value];
            });

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
                ->options($themeOptions)
                ->required(),
        ];
    }
}
