<?php declare(strict_types=1);

namespace Siteman\Cms\Settings;

use Siteman\Cms\Facades\Siteman;
use Illuminate\Support\Collection;
use Spatie\LaravelSettings\SettingsContainer;

class SitemanSettingsContainer extends SettingsContainer
{
    public function getSettingClasses(): Collection
    {
        $settings = parent::getSettingClasses()
            ->merge(Siteman::registeredSettings())
            ->unique();

        return self::$settingsClasses = $settings;
    }
}
