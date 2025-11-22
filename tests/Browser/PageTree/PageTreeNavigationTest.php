<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\PageResource;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page', 'delete_page'])->create());
});

it('clicks on page in tree to select and load edit form', function () {
    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => '/test',
        'parent_id' => null,
    ]);

    visit(PageResource::getUrl('tree'))
        ->assertSee($page->slug)
        ->click('[data-page-id="' . $page->id . '"]')
        ->assertSee($page->title);
});

it('switches edit context when clicking different pages', function () {
    $page1 = Page::factory()->create([
        'title' => 'First Page',
        'slug' => '/first',
        'parent_id' => null,
    ]);

    $page2 = Page::factory()->create([
        'title' => 'Second Page',
        'slug' => '/second',
        'parent_id' => null,
    ]);

    visit(PageResource::getUrl('tree'))
        ->assertSee($page1->slug)
        ->assertSee($page2->slug)
        // Click first page
        ->click('[data-page-id="' . $page1->id . '"]')
        ->assertSee($page1->title)
        // Click second page
        ->click('[data-page-id="' . $page2->id . '"]')
        ->assertSee($page2->title);
});

it('tree displays all pages with their slugs', function () {
    $page1 = Page::factory()->create([
        'title' => 'Page One',
        'slug' => '/page-1',
        'parent_id' => null,
    ]);

    $page2 = Page::factory()->create([
        'title' => 'Page Two',
        'slug' => '/page-2',
        'parent_id' => null,
    ]);

    visit(PageResource::getUrl('tree'))
        ->assertSee($page1->slug)
        ->assertSee($page2->slug);
});

it('expandable tree nodes display pages with children', function () {
    $parent = Page::factory()->create([
        'title' => 'Parent Page',
        'slug' => '/parent',
        'parent_id' => null,
    ]);

    $child1 = Page::factory()->create([
        'title' => 'Child One',
        'slug' => '/child-1',
        'parent_id' => $parent->id,
    ]);

    $child2 = Page::factory()->create([
        'title' => 'Child Two',
        'slug' => '/child-2',
        'parent_id' => $parent->id,
    ]);

    visit(PageResource::getUrl('tree'))
        ->assertSee($parent->slug)
        // Click chevron to expand parent node
        ->click('[data-sortable-item="' . $parent->id . '"] > div > div:first-child button[title="Expand"]')
        ->assertSee($child1->slug)
        ->assertSee($child2->slug);
});

it('navigates deep nested pages (3 levels) properly', function () {
    $level1 = Page::factory()->create([
        'title' => 'Level 1 Page',
        'slug' => '/level-1',
        'parent_id' => null,
    ]);

    $level2 = Page::factory()->create([
        'title' => 'Level 2 Page',
        'slug' => '/level-2',
        'parent_id' => $level1->id,
    ]);

    $level3 = Page::factory()->create([
        'title' => 'Level 3 Page',
        'slug' => '/level-3',
        'parent_id' => $level2->id,
    ]);

    visit(PageResource::getUrl('tree'))
        // Level 1 is visible as root
        ->assertSee($level1->slug)
        // Expand level 1 to see level 2
        ->click('[data-sortable-item="' . $level1->id . '"] > div > div:first-child button[title="Expand"]')
        ->assertSee($level2->slug)
        // Expand level 2 to see level 3
        ->click('[data-sortable-item="' . $level2->id . '"] > div > div:first-child button[title="Expand"]')
        ->assertSee($level3->slug)
        // Click level 1 to select it
        ->click('[data-page-id="' . $level1->id . '"]')
        ->assertSee($level1->title)
        // Click level 2 to select it
        ->click('[data-page-id="' . $level2->id . '"]')
        ->assertSee($level2->title)
        // Click level 3 to select it
        ->click('[data-page-id="' . $level3->id . '"]')
        ->assertSee($level3->title);
});

it('supports URL state with selected page ID', function () {
    $page1 = Page::factory()->create([
        'title' => 'First Page',
        'slug' => '/first',
        'parent_id' => null,
    ]);

    $page2 = Page::factory()->create([
        'title' => 'Second Page',
        'slug' => '/second',
        'parent_id' => null,
    ]);

    // Visit with selectedPageId in URL
    visit(PageResource::getUrl('tree', ['selectedPageId' => $page1->id]))
        ->assertSee($page1->title)
        ->assertSee($page1->slug);

    // Visit with different selectedPageId
    visit(PageResource::getUrl('tree', ['selectedPageId' => $page2->id]))
        ->assertSee($page2->title)
        ->assertSee($page2->slug);
});
