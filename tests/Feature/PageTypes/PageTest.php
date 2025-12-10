<?php

use Siteman\Cms\Models\Page;

it('shows only published pages', function () {
    createSite();

    Page::factory()
        ->create([
            'published_at' => null,
            'slug' => '/draft',
        ]);

    Page::factory()
        ->published()
        ->create([
            'slug' => '/published',
        ]);

    $this->get('/draft')->assertStatus(404);
    $this->get('/published')->assertStatus(200);
});

it('can render with a layout', function () {
    createSite();

    Page::factory()
        ->published()
        ->create([
            'slug' => '/published',
            'layout' => 'base-layout',
        ]);

    $this->get('/published')
        ->assertOk()
        ->assertViewIs('siteman::themes.layout')
        ->assertViewHas('layout', 'base-layout');
});
