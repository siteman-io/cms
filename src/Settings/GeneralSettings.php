<?php declare(strict_types=1);

namespace Siteman\Cms\Settings;

use Siteman\Cms\Theme\BlankTheme;
use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $site_name;

    public ?string $description;

    public string $theme = BlankTheme::class;

    public static function group(): string
    {
        return 'general';
    }
}
