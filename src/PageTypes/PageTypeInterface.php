<?php declare(strict_types=1);

namespace Siteman\Cms\PageTypes;

use Illuminate\Http\Request;
use Siteman\Cms\Models\Page;

interface PageTypeInterface
{
    public static function extendPageMainFields(array $fields): array;

    public static function extendPageSidebarFields(array $fields): array;

    public function render(Request $request, Page $page);
}
