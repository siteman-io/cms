<?php

use Siteman\Cms\Models\Post;

it('can list tags with published posts count', function () {
    Post::factory()->count(2)->published()->withTags(['foo', 'bar'])->create();

    $this->get('/tags')
        ->assertOk()
        ->assertSee(['foo (2)', 'bar (2)']);
});
