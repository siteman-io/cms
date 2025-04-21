<?php declare(strict_types=1);

namespace Siteman\Cms\PageTypes;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Http\Request;
use Siteman\Cms\Blocks\BlockBuilder;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Page as PageModel;
use Siteman\Cms\PageTypes\Concerns\InteractsWithPageForm;
use Siteman\Cms\PageTypes\Concerns\InteractsWithViews;

class Page implements PageTypeInterface
{
    use InteractsWithPageForm;
    use InteractsWithViews;

    public function render(Request $request, PageModel $page)
    {
        if ($page->layout) {
            return \view()->make(
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
                ->options(array_combine(array_keys(Siteman::getLayouts()), array_keys(Siteman::getLayouts()))),
            Textarea::make('description')
                ->label(__('siteman::page.fields.description.label'))
                ->helperText(__('siteman::page.fields.description.helper-text'))
                ->asPageMetaField(),
        ]);
    }
}
