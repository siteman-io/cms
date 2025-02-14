<?php

use Siteman\Cms\Models\Post;
use Siteman\Cms\Settings\BlogSettings;

it('shows posts index on /blog', function () {
    $posts = Post::factory()
        ->published()
        ->count(2)
        ->create();

    \Illuminate\Support\Facades\Route::getRoutes()->refreshNameLookups();
    $this->get(app(BlogSettings::class)->blog_index_route)->assertSeeText($posts->map->title->toArray());
});

it('shows only published posts', function () {})->todo();

it('needs permission to create posts', function () {})->todo();
it('can create posts', function () {})->todo();

it('needs permission to update posts', function () {})->todo();
it('can update posts', function () {})->todo();

it('needs permission to delete posts', function () {})->todo();
it('can delete posts', function () {})->todo();

it('properly resizes the featured image', function () {})->todo();
