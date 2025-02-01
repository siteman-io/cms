<?php

namespace Siteman\Cms\Http;

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Page;
use Illuminate\Support\Facades\Context;

class PagesController
{
    public function show(string $slug = '/')
    {
        $page = Page::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();
        Context::add('current_page', $page);

        return Siteman::theme()->render($page);
    }
}
