<?php declare(strict_types=1);

namespace Siteman\Cms\PageTypes;

use Siteman\Cms\Models\Page;

interface PageTypeInterface
{
    public function render(Page $page);
}
