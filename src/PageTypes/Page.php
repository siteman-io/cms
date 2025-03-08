<?php declare(strict_types=1);

namespace Siteman\Cms\PageTypes;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Siteman\Cms\Blocks\BlockBuilder;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Page as PageModel;
use Siteman\Cms\Theme\ThemeInterface;

class Page implements PageTypeInterface
{
    public function __construct(private readonly ThemeInterface $theme, private readonly Factory $view) {}

    public function render(Request $request, PageModel $page)
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

    public static function extendPageMainFields(array $fields): array
    {
        return array_merge($fields, [
            BlockBuilder::make('blocks'),
        ]);
    }

    public static function extendPageSidebarFields(array $fields): array
    {
        return array_merge($fields, [
            Select::make('layout')
                ->label(__('siteman::page.fields.layout.label'))
                ->helperText(__('siteman::page.fields.layout.helper-text'))
                ->options(array_keys(Siteman::getLayouts())),
            Textarea::make('description')
                ->label(__('siteman::page.fields.description.label'))
                ->helperText(__('siteman::page.fields.description.helper-text'))
                ->asPageMetaField(),
        ]);
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
