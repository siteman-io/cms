<?php declare(strict_types=1);

namespace Siteman\Cms\PageTypes;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Siteman\Cms\Models\Page as PageModel;
use Siteman\Cms\Theme\ThemeInterface;

class Page implements PageTypeInterface
{
    public function __construct(private readonly ThemeInterface $theme, private readonly Factory $view) {}

    public function render(PageModel $page)
    {
        if ($page->layout) {
            return $this->view->make(
                'siteman::themes.layout',
                ['page' => $page, 'layout' => $page->layout],
            );
        }

        return $this->renderView(
            [
                $this->getViewPath('pages.'.($page->slug !== '/' ? str($page->slug)->replace('/', '.')->toString() : 'home')),
                $this->getViewPath('pages.show'),
                'siteman::themes.blank.pages.show',
            ],
            ['page' => $page],
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
