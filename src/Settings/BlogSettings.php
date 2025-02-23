<?php declare(strict_types=1);

namespace Siteman\Cms\Settings;

use Spatie\LaravelSettings\Settings;

class BlogSettings extends Settings
{
    public bool $enabled;

    public string $blog_index_route;

    public string $tag_index_route;

    public bool $rss_enabled;

    public string $rss_endpoint;

    public static function group(): string
    {
        return 'blog';
    }
}
