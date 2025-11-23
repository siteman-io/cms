<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\PageResource;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'create_page', 'update_page', 'delete_page'])->create());
});

it('can verify create button exists in tree header', function () {
    visit(PageResource::getUrl('tree'))
        ->assertSee('New Page');
});

it('can navigate to create page via button', function () {
    visit(PageResource::getUrl('tree'))
        ->click('New Page')
        ->assertSee('Create Page');
});

it('can delete a leaf page without children via UI', function () {
    $page = Page::factory()->create(['slug' => '/to-delete']);

    visit(PageResource::getUrl('tree'))
        ->assertSee($page->slug)
        ->click('[data-sortable-item="'.$page->id.'"] > div > div:last-child [aria-label="Actions"]')
        ->click('Delete')
        ->press('Confirm');

    // Verify page is removed from UI
    visit(PageResource::getUrl('tree'))
        ->assertDontSee($page->slug);
});

it('shows delete options modal for pages with children', function () {
    $parent = Page::factory()->create(['slug' => '/parent']);
    Page::factory()->create(['slug' => '/child', 'parent_id' => $parent->id]);

    visit(PageResource::getUrl('tree'))
        ->assertSee($parent->slug)
        ->click('[data-sortable-item="'.$parent->id.'"] > div > div:last-child [aria-label="Actions"]')
        ->click('Delete')
        ->assertSee('This page has child pages');
});

// Note: Cascade and reassign delete logic is comprehensively tested in
// tests/Feature/PageTree/PageRelationshipsTest.php
