<?php declare(strict_types=1);

namespace Siteman\Cms\Http\Actions;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Siteman\Cms\Models\Page;
use Siteman\Cms\View\Renderer;

class ShowPage
{
    public function __construct(private readonly Renderer $renderer) {}

    public function __invoke(Request $request): View
    {
        $page = Page::query()
            ->published()
            ->where('slug', $request->path())
            ->firstOrFail();
        Context::add('current_page', $page);

        return $this->renderer->renderPostType($page);
    }
}
