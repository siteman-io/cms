<?php

namespace Siteman\Cms\Http;

use Siteman\Cms\Facades\Siteman\Cms\PageTypes\PageTypeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Page;

class SitemanController
{
    public function __invoke(Request $request): mixed
    {
        $page = Page::published()->where('computed_slug', '/'.ltrim($request->path(), '/'))->first();
        if (!$page) {
            $rootPath = '/'.str($request->path())->before('/');
            $page = Page::published()->where('computed_slug', $rootPath)->firstOrFail();
        }

        /** @var PageTypeInterface $pageType */
        $pageType = app(Siteman::getPageTypes()[$page->type]);

        Context::add('current_page', $page);

        return $pageType->render($request, $page);
    }
}
