<?php

use Siteman\Cms\Models\Page;

it('can render a blog index page', function () {
    $blog = Page::factory()->published()->create([
        'type' => 'blog_index',
        'title' => 'Blog Index',
        'slug' => '/blog',
    ]);
    $posts = Page::factory()->count(5)->published()->create([
        'parent_id' => $blog->id,
    ]);

    $response = $this->get($blog->computed_slug);

    $response->assertStatus(200);
    $response->assertSee($posts->map->title->all());
});
