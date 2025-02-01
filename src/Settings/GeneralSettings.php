<?php

declare(strict_types=1);

namespace Siteman\Cms\Settings;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings implements SitemanSettingsInterface
{
    public ?string $site_name;

    public ?string $description;

    public static function group(): string
    {
        return 'general';
    }

    public static function icon(): string
    {
        return 'heroicon-o-globe-alt';
    }

    public function form(): array
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

    public function submit(array $payload): void
    {
        $this->site_name = $payload['site_name'];
        $this->description = $payload['description'];
        $this->save();
    }
}
