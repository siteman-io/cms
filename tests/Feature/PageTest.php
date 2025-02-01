<?php

use Siteman\Cms\Models\Page;

it('shows pages via their slug', function () {
    $this->withoutExceptionHandling();
    $title = 'test-title-to-compare';

    Page::factory()
        ->published()
        ->create([
            'title' => $title,
            'slug' => 'test',
        ]);

    $this->get('/test')->assertSeeText($title);
});

it('shows only published pages', function () {})->todo();

it('needs permission to create pages', function () {})->todo();
it('can create pages', function () {})->todo();

it('needs permission to update pages', function () {})->todo();
it('can update pages', function () {})->todo();

it('needs permission to delete pages', function () {})->todo();
it('can delete pages', function () {})->todo();
