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
        $page = Page::where('computed_slug', '/'.ltrim($request->path(), '/'))->first();
        if (!$page) {
            $rootPath = '/'.str($request->path())->before('/');
            $page = Page::where('computed_slug', $rootPath)->firstOrFail();
        }

        /** @var \Siteman\Cms\PageTypes\PageTypeInterface $pageType */
        $pageType = app(Siteman::getPageTypes()[$page->type]);

        Context::add('current_page', $page);

        return $pageType->render($request, $page);
    }
}
