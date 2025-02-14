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

    public static function isEnabled(): mixed
    {
        try {
            return app(self::class)->blog_index_route;
        } catch (\Throwable) {
            return false;
        }
    }

    public function getBlogIndexRoute(): string
    {
        return ltrim($this->blog_index_route, '/');
    }

    public function getRssEndpoint(): string
    {
        return ltrim($this->rss_endpoint, '/');
    }

    public function getTagRoutePrefix()
    {
        return ltrim($this->tag_route_prefix, '/');
    }
}
