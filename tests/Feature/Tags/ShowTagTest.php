<?php

use Siteman\Cms\Models\Page;

it('can list tags with published pages count', function () {
    Page::factory()->published()->create(['type' => 'tag_index', 'slug' => '/tags']);
    Page::factory()->count(2)->published()->withTags(['foo', 'bar'])->create();

    $this->get('/tags')
        ->assertOk()
        ->assertSee(['foo (2)', 'bar (2)']);
});
