<?php declare(strict_types=1);

namespace Siteman\Cms\PageTypes\Concerns;

trait InteractsWithPageForm
{
    public static function extendPageMainFields(array $fields): array
    {
        return $fields;
    }

    public static function extendPageSidebarFields(array $fields): array
    {
        return $fields;
    }
}
