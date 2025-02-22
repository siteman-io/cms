<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;

it('shows only published pages', function () {
    Page::factory()
        ->create([
            'published_at' => null,
            'slug' => 'draft',
        ]);

    Page::factory()
        ->published()
        ->create([
            'slug' => 'published',
        ]);

    $this->get('/draft')->assertStatus(404);
    $this->get('/published')->assertStatus(200);
});
