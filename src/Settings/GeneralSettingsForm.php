<?php declare(strict_types=1);

namespace Siteman\Cms\Settings;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

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
                ->label('siteman::pages/settings.groups.general.fields.site_name.label')
                ->translateLabel()
                ->helperText(__('siteman::pages/settings.groups.general.fields.site_name.helper-text'))
                ->required(),
            Textarea::make('description')
                ->label('siteman::pages/settings.groups.general.fields.description.label')
                ->translateLabel()
                ->helperText(__('siteman::pages/settings.groups.general.fields.description.helper-text'))
                ->rows(2),
        ];
    }
}
