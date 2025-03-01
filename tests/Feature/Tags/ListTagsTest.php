<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;

it('can list tags with published posts count', function () {
    $posts = Page::factory()->count(2)->published()->withTags(['foo', 'bar'])->create();

    $this->get('/tags/foo')
        ->assertOk()
        ->assertSee($posts[0]->title)
        ->assertSee($posts[1]->title);

    $this->get('/tags/bar')
        ->assertOk()
        ->assertSee($posts[0]->title)
        ->assertSee($posts[1]->title);
});
