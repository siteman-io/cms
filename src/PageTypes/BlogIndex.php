<?php declare(strict_types=1);

namespace Siteman\Cms\PageTypes;

use Illuminate\Http\Request;
use Siteman\Cms\Models\Page as PageModel;
use Siteman\Cms\PageTypes\Concerns\InteractsWithPageForm;
use Siteman\Cms\PageTypes\Concerns\InteractsWithViews;

class BlogIndex implements PageTypeInterface
{
    use InteractsWithPageForm;
    use InteractsWithViews;

    public function render(Request $request, PageModel $page)
    {
        return $this->renderView(
            [
                $this->getViewPath('posts.index'),
                'siteman::themes.blank.posts.index',
            ],
            ['posts' => $page->children()->published()->paginate(10)],
        );
    }
}
