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

it('can delete a leaf page without children', function () {
    $page = Page::factory()->create(['slug' => '/to-delete']);

    visit(PageResource::getUrl('tree'))
        ->assertSee($page->slug)
        ->click('[data-sortable-item="' . $page->id . '"] > div > div:last-child [aria-label="Actions"]')
        ->click('Delete')
        ->press('Confirm');

    // Verify page is removed
    visit(PageResource::getUrl('tree'))
        ->assertDontSee($page->slug);

    expect(Page::find($page->id))->toBeNull();
});

it('shows delete options modal for pages with children', function () {
    $parent = Page::factory()->create(['slug' => '/parent']);
    Page::factory()->create(['slug' => '/child', 'parent_id' => $parent->id]);

    visit(PageResource::getUrl('tree'))
        ->assertSee($parent->slug)
        ->click('[data-sortable-item="' . $parent->id . '"] > div > div:last-child [aria-label="Actions"]')
        ->click('Delete')
        ->assertSee('This page has child pages');
});

it('can cascade delete parent and all children', function () {
    $parent = Page::factory()->create(['slug' => '/parent']);
    $child = Page::factory()->create(['slug' => '/child', 'parent_id' => $parent->id]);

    visit(PageResource::getUrl('tree'))
        ->click('[data-sortable-item="' . $parent->id . '"] > div > div:last-child [aria-label="Actions"]')
        ->click('Delete')
        ->click('Delete all child pages') // Select cascade option
        ->press('Confirm');

    // Verify both are gone
    visit(PageResource::getUrl('tree'))
        ->assertDontSee($parent->slug)
        ->assertDontSee($child->slug);

    expect(Page::find($parent->id))->toBeNull();
    expect(Page::find($child->id))->toBeNull();
});

it('can reassign children before deleting parent', function () {
    $parent = Page::factory()->create(['slug' => '/parent']);
    $child = Page::factory()->create(['slug' => '/child', 'parent_id' => $parent->id]);

    visit(PageResource::getUrl('tree'))
        ->click('[data-sortable-item="' . $parent->id . '"] > div > div:last-child [aria-label="Actions"]')
        ->click('Delete')
        ->click('Move child pages to parent level') // Select reassign option
        ->press('Confirm');

    // Verify parent gone, child at root
    visit(PageResource::getUrl('tree'))
        ->assertSee($child->slug)
        ->assertDontSee($parent->slug);

    expect(Page::find($parent->id))->toBeNull();
    expect($child->fresh()->parent_id)->toBeNull();
});
