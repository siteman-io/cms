<?php

use Siteman\Cms\Models\Page;

it('shows an rss feed', function () {
    createSite();

    Page::factory()->published()->create(['type' => 'rss_feed', 'slug' => '/rss']);

    $posts = Page::factory()
        ->count(5)
        ->published()
        ->create();

    $this->get('/rss')
        ->assertStatus(200)
        ->assertSee($posts->map->title->all());
});
