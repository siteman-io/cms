<?php declare(strict_types=1);

namespace Siteman\Cms\Settings;

use Spatie\LaravelSettings\Settings;

class BlogSettings extends Settings
{
    public bool $enabled;

    public string $blog_index_route;

    public string $tag_route_prefix;

    public bool $rss_enabled;

    public string $rss_endpoint;

    public static function group(): string
    {
        return 'blog';
    }

    public static function isEnabled(): bool
    {
        try {
            return app(self::class)->enabled;
        } catch (\Throwable) {
            return false;
        }
    }
}
