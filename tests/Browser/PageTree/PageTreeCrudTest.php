<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\PageResource;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(createUser(permissions: ['view_any_page', 'create_page', 'update_page', 'delete_page']));
});

it('can verify create button exists in tree header', function () {
    visit(PageResource::getUrl())
        ->assertSee('New Page');
});

it('can navigate to create page via button', function () {
    visit(PageResource::getUrl())
        ->click('New Page')
        ->assertSee('Create Page');
});

it('can delete a leaf page without children via UI', function () {
    $page = Page::factory()->create(['slug' => '/to-delete']);

    visit(PageResource::getUrl())
        ->assertSee($page->slug)
        ->click('[data-sortable-item="'.$page->id.'"] > div > div:last-child [aria-label="Actions"]')
        ->click('Delete')
        ->press('Confirm');

    visit(PageResource::getUrl())
        ->assertDontSee($page->slug);
});

it('shows delete options modal for pages with children', function () {
    $parent = Page::factory()
        ->withChildren(1)
        ->create(['slug' => '/parent']);

    visit(PageResource::getUrl())
        ->assertSee($parent->slug)
        ->click('[data-sortable-item="'.$parent->id.'"] > div > div:last-child [aria-label="Actions"]')
        ->click('Delete')
        ->assertSee('This page has child pages');
});
