<?php

namespace Siteman\Cms\Http;

use Illuminate\Http\Request;
use Siteman\Cms\Http\Actions\ShowBlogIndex;
use Siteman\Cms\Http\Actions\ShowBlogPost;
use Siteman\Cms\Http\Actions\ShowPage;
use Siteman\Cms\Http\Actions\ShowRssFeed;
use Siteman\Cms\Http\Actions\ShowTag;
use Siteman\Cms\Settings\BlogSettings;

class SitemanController
{
    public function __invoke(Request $request, BlogSettings $blogSettings): mixed
    {
        return match ($this->route($request, $blogSettings)) {
            'page' => app(ShowPage::class)($request),
            'post' => app(ShowBlogPost::class)($request, $blogSettings),
            'post_index' => app(ShowBlogIndex::class)(),
            'tag' => app(ShowTag::class)($request, $blogSettings),
            'rss' => app(ShowRssFeed::class)($request),
            default => abort(404),
        };
    }

    private function route(Request $request, BlogSettings $blogSettings): string
    {
        $path = $request->path();
        if ($blogSettings->enabled) {
            if ($path === $blogSettings->getBlogIndexRoute()) {
                return 'post_index';
            }
            if (str_starts_with($path, $blogSettings->getBlogIndexRoute().'/')) {
                return 'post';
            }
            if ($blogSettings->rss_enabled === true && $path === $blogSettings->getRssEndpoint()) {
                return 'rss';
            }

            if (str_starts_with($path, $blogSettings->getTagRoutePrefix().'/')) {
                return 'tag';
            }
        }

        return 'page';
    }
}
