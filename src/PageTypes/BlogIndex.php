<?php declare(strict_types=1);

namespace Siteman\Cms\PageTypes;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Siteman\Cms\Models\Page as PageModel;
use Siteman\Cms\Theme\ThemeInterface;

class BlogIndex implements PageTypeInterface
{
    public function __construct(private readonly ThemeInterface $theme, private readonly Factory $view) {}

    public function render(Request $request, PageModel $page)
    {
        return $this->renderView(
            [
                $this->getViewPath('posts.index'),
                'siteman::themes.blank.posts.index',
            ],
            ['posts' => $page->children()->paginate(10)],
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
