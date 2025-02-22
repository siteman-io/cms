<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;

it('does not show the table of contents by default', function () {
    Page::factory()
        ->published()
        ->withMarkdownBlock()
        ->create([
            'slug' => 'markdown',
        ]);

    $this->get('/markdown')->assertStatus(200)->assertDontSeeHtml('<ul class="table-of-contents">');
});

it('can show the table of contents', function () {
    Page::factory()
        ->published()
        ->withMarkdownBlock(true)
        ->create([
            'slug' => 'markdown',
        ]);

    $this->get('/markdown')->assertStatus(200)->assertSeeHtml('<ul class="table-of-contents">');
});
