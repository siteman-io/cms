<?php declare(strict_types=1);

namespace Siteman\Cms\Http\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Page;

class ShowPage
{
    public function __invoke(Request $request)
    {
        $page = Page::query()
            ->published()
            ->where('slug', $request->path())
            ->firstOrFail();
        Context::add('current_page', $page);

        return Siteman::theme()->render($page);
    }
}
