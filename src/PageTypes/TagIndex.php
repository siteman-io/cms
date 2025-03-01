<?php declare(strict_types=1);

namespace Siteman\Cms\PageTypes;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Siteman\Cms\Models\Page as PageModel;
use Siteman\Cms\Models\Tag;
use Siteman\Cms\Theme\ThemeInterface;

class TagIndex implements PageTypeInterface
{
    public function __construct(private readonly ThemeInterface $theme, private readonly Factory $view) {}

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

    protected function getViewPath(string $view): string
    {
        $prefix = method_exists($this->theme, 'getViewPrefix')
            ? $this->theme->getViewPrefix()
            : Str::of(class_basename($this->theme))->before('Theme')->kebab()->prepend('themes.');

        return $prefix.'.'.$view;
    }

    protected function renderView(string|array $views, array $data = []): View
    {
        $views = Arr::wrap($views);

        foreach ($views as $view) {
            if ($this->view->exists($view)) {
                return $this->view->make($view, $data);
            }
        }
        throw new \Exception('No view found for the keys: '.implode(', ', $views));
    }
}
