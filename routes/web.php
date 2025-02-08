<?php

use Siteman\Cms\Http\FeedController;
use Siteman\Cms\Http\Middleware\InjectAdminBar;
use Siteman\Cms\Http\PagesController;
use Siteman\Cms\Http\PostsController;
use Siteman\Cms\Http\TagsController;
use Siteman\Cms\Settings\BlogSettings;

Route::middleware(['web', InjectAdminBar::class])->group(function () {

    try {
        $settings = app(BlogSettings::class);
        if ($settings->enabled) {
            Route::get($settings->blog_index_route, [PostsController::class, 'index'])->name('siteman.posts.index');
            Route::get($settings->blog_index_route.'/{slug}', [PostsController::class, 'show'])->name('siteman.posts.show');
            Route::get($settings->tag_route_prefix.'/{slug}', [TagsController::class, 'show'])->name('siteman.tags.show');
        }

        if ($settings->rss_enabled) {
            Route::get($settings->rss_endpoint, FeedController::class)->name('siteman.feed');
        }
    } catch (\Throwable) {

    }

    Route::get('/{slug?}', [PagesController::class, 'show'])->where('slug', '.*')->name('siteman.page')->fallback();
});
