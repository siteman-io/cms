<?php

namespace Siteman\Cms\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Siteman\Cms\Models\Page;
use Siteman\Cms\View\Renderer;

class SitemanController
{
    public function __construct(private readonly Renderer $renderer) {}

    public function __invoke(Request $request): mixed
    {
        $path = '/'.ltrim($request->path(), '/');

        $page = Page::where('computed_slug', $path)->firstOrFail();

        Context::add('current_page', $page);

        return $this->renderer->renderPostType($page);
    }
}
