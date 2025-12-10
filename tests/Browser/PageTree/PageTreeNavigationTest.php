<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\PageResource;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(createUser(permissions: ['view_any_page', 'update_page', 'delete_page']));
});

it('clicks on page in tree to select and load edit form', function () {
    $page = Page::factory()->create(['slug' => '/test']);

    visit(PageResource::getUrl())
        ->assertSee($page->slug)
        ->click('[data-page-id="'.$page->id.'"]')
        ->assertSee($page->title);
});

it('switches edit context when clicking different pages', function () {
    $page1 = Page::factory()->create(['slug' => '/first']);
    $page2 = Page::factory()->create(['slug' => '/second']);

    visit(PageResource::getUrl())
        ->assertSee($page1->slug)
        ->assertSee($page2->slug)
        ->click('[data-page-id="'.$page1->id.'"]')
        ->assertSee($page1->title)
        ->click('[data-page-id="'.$page2->id.'"]')
        ->assertSee($page2->title);
});

it('expands tree nodes to show nested children', function () {
    $parent = Page::factory()
        ->withChildren([
            ['slug' => '/child-1'],
            ['slug' => '/child-2'],
        ])
        ->create(['slug' => '/parent']);

    visit(PageResource::getUrl())
        ->assertSee($parent->slug)
        ->click('[data-page-expand="'.$parent->id.'"]')
        ->assertSee('/child-1')
        ->assertSee('/child-2');
});

it('expands deep nested pages across 3 levels', function () {
    $level1 = Page::factory()
        ->withChildren(
            Page::factory()
                ->state(['slug' => '/level-2'])
                ->withChildren(Page::factory()->state(['slug' => '/level-3']))
        )
        ->create(['slug' => '/level-1']);

    $level2 = $level1->children->first();

    visit(PageResource::getUrl())
        ->assertSee($level1->slug)
        ->click('[data-page-expand="'.$level1->id.'"]')
        ->assertSee($level2->slug)
        ->click('[data-page-expand="'.$level2->id.'"]')
        ->assertSee('/level-3');
});

it('supports URL state with selected page ID parameter', function () {
    $page = Page::factory()->create(['slug' => '/test']);

    visit(PageResource::getUrl('index', ['selectedPageId' => $page->id]))
        ->assertSee($page->slug);
});
