<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\PageResource;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page', 'delete_page'])->create());
});

// Note: Actual drag-and-drop simulation with SortableJS in browser automation is complex
// and not reliably supported by Pest's browser plugin. The reorder functionality itself
// is thoroughly tested in Feature tests (PageTreeComponentTest). These browser tests
// verify the tree UI reflects hierarchy changes correctly.

it('displays pages in hierarchical order', function () {
    $parent = Page::factory()->create(['slug' => '/parent']);
    Page::factory()->create(['slug' => '/child-1', 'parent_id' => $parent->id]);
    Page::factory()->create(['slug' => '/child-2', 'parent_id' => $parent->id]);

    visit(PageResource::getUrl('tree'))
        ->assertSee($parent->slug)
        ->click('[data-sortable-item="'.$parent->id.'"] > div > div:first-child button[title="Expand"]')
        ->assertSee('/child-1')
        ->assertSee('/child-2');
});

it('reflects hierarchy changes after moving page to different parent', function () {
    $parent = Page::factory()->create(['slug' => '/parent']);
    $page = Page::factory()->create(['slug' => '/standalone']);

    visit(PageResource::getUrl('tree'))
        ->assertSee($parent->slug)
        ->assertSee($page->slug);

    $page->update(['parent_id' => $parent->id]);

    visit(PageResource::getUrl('tree'))
        ->assertSee($parent->slug)
        ->click('[data-sortable-item="'.$parent->id.'"] > div > div:first-child button[title="Expand"]')
        ->assertSee($page->slug);
});

it('shows page at root level after removing parent', function () {
    $parent = Page::factory()->create(['slug' => '/parent']);
    $child = Page::factory()->create(['slug' => '/child', 'parent_id' => $parent->id]);

    visit(PageResource::getUrl('tree'))
        ->assertSee($parent->slug)
        ->click('[data-sortable-item="'.$parent->id.'"] > div > div:first-child button[title="Expand"]')
        ->assertSee($child->slug);

    $child->update(['parent_id' => null]);

    visit(PageResource::getUrl('tree'))
        ->assertSee($parent->slug)
        ->assertSee($child->slug);
});
