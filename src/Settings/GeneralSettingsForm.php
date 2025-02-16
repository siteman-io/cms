<?php declare(strict_types=1);

namespace Siteman\Cms\Settings;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Siteman\Cms\Theme\BlankTheme;

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
                ->label(__('siteman::pages/settings.groups.general.fields.site_name.label'))
                ->helperText(__('siteman::pages/settings.groups.general.fields.site_name.helper-text'))
                ->required(),
            Textarea::make('description')
                ->label(__('siteman::pages/settings.groups.general.fields.description.label'))
                ->helperText(__('siteman::pages/settings.groups.general.fields.description.helper-text'))
                ->rows(2),
            Select::make('theme')
                ->label(__('siteman::pages/settings.groups.general.fields.theme.label'))
                ->helperText(__('siteman::pages/settings.groups.general.fields.theme.helper-text'))
                ->options([
                    BlankTheme::class => BlankTheme::getName(),
                ])
                ->required(),
        ];
    }
}
