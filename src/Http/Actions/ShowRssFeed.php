<?php declare(strict_types=1);

namespace Siteman\Cms\Http\Actions;

use Illuminate\Http\Request;
use Siteman\Cms\Models\Post;
use Siteman\Cms\Settings\GeneralSettings;
use Spatie\Feed\Feed;
use Spatie\Feed\Helpers\ResolveFeedItems;

class ShowRssFeed
{
    public function __invoke(Request $request): Feed
    {
        $items = ResolveFeedItems::resolve('main', [Post::class, 'getFeedItems']);
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
