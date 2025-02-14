<?php

namespace Siteman\Cms\Resources\PostResource\Widgets;

use Filament\Widgets\Widget;
use Siteman\Cms\Settings\BlogSettings;

class LatestPostWidget extends Widget
{
    protected static string $view = 'siteman::resources.post-resource.widgets.latest-post-widget';

    public static function isDiscovered(): bool
    {
        return app(BlogSettings::class)->enabled;
    }
}
