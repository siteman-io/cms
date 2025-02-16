<?php declare(strict_types=1);

use Siteman\Cms\Models\Post;

it('shows only published posts', function () {
    Post::factory()
        ->create([
            'published_at' => null,
            'slug' => 'draft',
        ]);

    Post::factory()
        ->published()
        ->create([
            'slug' => 'published',
        ]);

    $this->get('/blog/draft')->assertStatus(404);
    $this->get('/blog/published')->assertStatus(200);
});
