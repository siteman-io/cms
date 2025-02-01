<?php

use Siteman\Cms\Http\FeedController;
use Siteman\Cms\Http\Middleware\InjectAdminBar;
use Siteman\Cms\Http\PagesController;
use Siteman\Cms\Http\PostsController;
use Siteman\Cms\Http\TagsController;

Route::middleware(['web', InjectAdminBar::class])->group(function () {

    Route::get('/blog', [PostsController::class, 'index'])->name('siteman.posts.index');
    Route::get('/blog/{slug}', [PostsController::class, 'show'])->name('siteman.posts.show');
    Route::get('/tags/{slug}', [TagsController::class, 'show'])->name('siteman.tags.show');

    Route::get('/rss', FeedController::class)->name('siteman.feed');

    Route::get('/{slug?}', [PagesController::class, 'show'])->where('slug', '.*')->name('siteman.page')->fallback();
});
