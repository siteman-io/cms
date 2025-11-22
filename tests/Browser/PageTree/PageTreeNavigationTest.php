<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\PageResource;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page', 'delete_page'])->create());
});

it('clicks on page in tree to select and load edit form', function () {
    $page = Page::factory()->create(['slug' => '/test']);

    visit(PageResource::getUrl('tree'))
        ->assertSee($page->slug)
        ->click('[data-page-id="'.$page->id.'"]')
        ->assertSee($page->title);
});

it('switches edit context when clicking different pages', function () {
    $page1 = Page::factory()->create(['slug' => '/first']);
    $page2 = Page::factory()->create(['slug' => '/second']);

    visit(PageResource::getUrl('tree'))
        ->assertSee($page1->slug)
        ->assertSee($page2->slug)
        ->click('[data-page-id="'.$page1->id.'"]')
        ->assertSee($page1->title)
        ->click('[data-page-id="'.$page2->id.'"]')
        ->assertSee($page2->title);
});

it('expands tree nodes to show nested children', function () {
    $parent = Page::factory()->create(['slug' => '/parent']);
    Page::factory()->create(['slug' => '/child-1', 'parent_id' => $parent->id]);
    Page::factory()->create(['slug' => '/child-2', 'parent_id' => $parent->id]);

    visit(PageResource::getUrl('tree'))
        ->assertSee($parent->slug)
        ->click('[data-sortable-item="'.$parent->id.'"] > div > div:first-child button[title="Expand"]')
        ->assertSee('/child-1')
        ->assertSee('/child-2');
});

it('expands deep nested pages across 3 levels', function () {
    $level1 = Page::factory()->create(['slug' => '/level-1']);
    $level2 = Page::factory()->create(['slug' => '/level-2', 'parent_id' => $level1->id]);
    $level3 = Page::factory()->create(['slug' => '/level-3', 'parent_id' => $level2->id]);

    visit(PageResource::getUrl('tree'))
        ->assertSee($level1->slug)
        ->click('[data-sortable-item="'.$level1->id.'"] > div > div:first-child button[title="Expand"]')
        ->assertSee($level2->slug)
        ->click('[data-sortable-item="'.$level2->id.'"] > div > div:first-child button[title="Expand"]')
        ->assertSee($level3->slug);
});

it('supports URL state with selected page ID parameter', function () {
    $page = Page::factory()->create(['slug' => '/test']);

    visit(PageResource::getUrl('tree', ['selectedPageId' => $page->id]))
        ->assertSee($page->title)
        ->assertSee($page->slug);
});
