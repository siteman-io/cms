<?php declare(strict_types=1);

namespace Siteman\Cms\PageTypes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Siteman\Cms\Models\Page as PageModel;
use Siteman\Cms\Models\Tag;
use Siteman\Cms\PageTypes\Concerns\InteractsWithPageForm;
use Siteman\Cms\PageTypes\Concerns\InteractsWithViews;

class TagIndex implements PageTypeInterface
{
    use InteractsWithPageForm;
    use InteractsWithViews;

    public function render(Request $request, PageModel $page)
    {
        $tag = str_replace($page->computed_slug, '', '/'.ltrim($request->path(), '/'));
        if ($tag !== '') {
            $tag = Tag::where('slug->en', ltrim($tag, '/'))->firstOrFail();
            Context::add('current_tag', $tag);
            $pages = PageModel::published()->withAnyTags([$tag])->paginate(5);

            return $this->renderView(
                [
                    $this->getViewPath('tags.'.str($tag->slug)->replace('/', '.')->toString()),
                    $this->getViewPath('tags.show'),
                    'siteman::themes.blank.tags.show',
                ],
                [
                    'tag' => $tag,
                    'pages' => $pages,
                ],
            );
        }
        $tags = Tag::withCount('pages')->orderBy('slug->en')->paginate();

        return $this->renderView(
            [
                $this->getViewPath('tags.index'),
                'siteman::themes.blank.tags.index',
            ],
            [
                'tagIndexPage' => $page,
                'tags' => $tags,
            ],
        );
    }
}
