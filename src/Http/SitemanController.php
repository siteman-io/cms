<?php

namespace Siteman\Cms\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Page;

class SitemanController
{
    public function __invoke(Request $request): mixed
    {
        $path = '/'.ltrim($request->path(), '/');

        $page = Page::where('computed_slug', $path)->firstOrFail();

        /** @var \Siteman\Cms\PageTypes\PageTypeInterface $pageType */
        $pageType = app(Siteman::getPageTypes()[$page->type]);

        Context::add('current_page', $page);

        return $pageType->render($page);
    }
}
