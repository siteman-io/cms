<?php declare(strict_types=1);

namespace Siteman\Cms\PageTypes;

use Illuminate\Http\Request;
use Siteman\Cms\Models\Page as PageModel;
use Siteman\Cms\Settings\GeneralSettings;
use Spatie\Feed\Feed;
use Spatie\Feed\Helpers\ResolveFeedItems;

class RssFeed implements PageTypeInterface
{
    public function render(Request $request, PageModel $page)
    {
        $items = ResolveFeedItems::resolve('main', [PageModel::class, 'getFeedItems']);
        $settings = app(GeneralSettings::class);

        return new Feed(
            $settings->site_name,
            $items,
            $request->url(),
            'feed::atom',
            $settings->description,
            'en-US',
            '',
            'atom',
            '',
        );
    }
}
