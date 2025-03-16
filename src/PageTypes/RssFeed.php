<?php declare(strict_types=1);

namespace Siteman\Cms\PageTypes;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Http\Request;
use Siteman\Cms\Models\Page as PageModel;
use Siteman\Cms\PageTypes\Concerns\InteractsWithPageForm;
use Siteman\Cms\Settings\GeneralSettings;
use Spatie\Feed\Feed;
use Spatie\Feed\Helpers\ResolveFeedItems;

class RssFeed implements PageTypeInterface
{
    use InteractsWithPageForm;

    public function render(Request $request, PageModel $page)
    {
        $items = ResolveFeedItems::resolve('main', [PageModel::class, 'getFeedItems']);
        $settings = app(GeneralSettings::class);

        return new Feed(
            $page->getMeta('feed_title', $settings->site_name),
            $items,
            $request->url(),
            'feed::atom',
            $page->getMeta('feed_description', $settings->description),
            $page->getMeta('feed_language', 'en-US'),
            '',
            'atom',
            '',
        );
    }

    public static function extendPageMainFields(array $fields): array
    {
        return array_merge($fields, [
            TextInput::make('feed_title')
                ->label('Feed Title')
                ->asPageMetaField(),
            Textarea::make('feed_description')
                ->rows(2)
                ->label('Feed Description')
                ->asPageMetaField(),
            Select::make('feed_language')
                ->options(
                    collect(\ResourceBundle::getLocales(''))
                        ->mapWithKeys(fn ($locale) => [$locale => \Locale::getDisplayName($locale, 'en_US')])
                        ->sort()
                        ->all()
                )->asPageMetaField(),
        ]);
    }
}
