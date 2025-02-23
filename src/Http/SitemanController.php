<?php

namespace Siteman\Cms\Http;

use Illuminate\Http\Request;
use Siteman\Cms\Http\Actions\ShowBlogIndex;
use Siteman\Cms\Http\Actions\ShowBlogPost;
use Siteman\Cms\Http\Actions\ShowPage;
use Siteman\Cms\Http\Actions\ShowRssFeed;
use Siteman\Cms\Http\Actions\ShowTag;
use Siteman\Cms\Http\Actions\ShowTagIndex;
use Siteman\Cms\Settings\BlogSettings;

class SitemanController
{
    public function __invoke(Request $request, BlogSettings $blogSettings): mixed
    {
        return match ($this->route($request, $blogSettings)) {
            'post_index' => app(ShowBlogIndex::class)(),
            'post' => app(ShowBlogPost::class)($request, $blogSettings),
            'tag_index' => app(ShowTagIndex::class)(),
            'tag' => app(ShowTag::class)($request, $blogSettings),
            'rss' => app(ShowRssFeed::class)($request),
            'page' => app(ShowPage::class)($request),
            default => abort(404),
        };
    }

    private function route(Request $request, BlogSettings $blogSettings): string
    {
        $path = $request->path();
        if ($blogSettings->enabled) {
            if ($path === $blogSettings->blog_index_route) {
                return 'post_index';
            }
            if (str_starts_with($path, $blogSettings->blog_index_route.'/')) {
                return 'post';
            }
            if ($path === $blogSettings->tag_index_route) {
                return 'tag_index';
            }
            if (str_starts_with($path, $blogSettings->tag_index_route.'/')) {
                return 'tag';
            }
            if ($blogSettings->rss_enabled === true && $path === $blogSettings->rss_endpoint) {
                return 'rss';
            }
        }

        return 'page';
    }
}
