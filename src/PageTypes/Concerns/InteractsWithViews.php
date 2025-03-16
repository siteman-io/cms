<?php declare(strict_types=1);

namespace Siteman\Cms\PageTypes\Concerns;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Siteman\Cms\Theme\ThemeInterface;

trait InteractsWithViews
{
    protected ?ThemeInterface $theme = null;

    protected function getViewPath(string $view): string
    {
        $prefix = method_exists($this->getTheme(), 'getViewPrefix')
            ? $this->getTheme()->getViewPrefix()
            : Str::of(class_basename($this->getTheme()))->before('Theme')->kebab()->prepend('themes.');

        return $prefix.'.'.$view;
    }

    protected function renderView(string|array $views, array $data = []): View
    {
        $views = Arr::wrap($views);

        foreach ($views as $view) {
            if (\view()->exists($view)) {
                return \view()->make($view, $data);
            }
        }
        throw new \Exception('No view found for the keys: '.implode(', ', $views));
    }

    protected function getTheme(): ThemeInterface
    {
        if ($this->theme) {
            return $this->theme;
        }
        $this->theme = app(ThemeInterface::class);

        return $this->theme;
    }
}
