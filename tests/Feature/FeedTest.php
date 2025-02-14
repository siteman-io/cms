<?php

use Siteman\Cms\Models\Post;
use Siteman\Cms\Settings\BlogSettings;

it('shows an rss feed', function () {
    $posts = Post::factory()
        ->count(5)
        ->published()
        ->create();

    $this->get('/rss')
        ->assertStatus(200)
        ->assertSee($posts->map->title->all());
});

it('can be disabled', function () {
    BlogSettings::fake([
        'rss_enabled' => false,
    ]);

    $this->get('/rss')->assertStatus(404);
});

it('can change the endpoint', function () {
    BlogSettings::fake([
        'rss_endpoint' => 'foo',
    ]);

    $posts = Post::factory()
        ->count(5)
        ->published()
        ->create();

    $this->get('/foo')
        ->assertStatus(200)
        ->assertSee($posts->map->title->all());
});
