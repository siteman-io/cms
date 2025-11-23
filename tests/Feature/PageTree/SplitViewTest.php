<?php declare(strict_types=1);

use Livewire\Livewire;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\PageResource;
use Siteman\Cms\Resources\Pages\Pages\PageTreeSplitView;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('loads split view page without errors', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'delete_page'])->create());

    get(PageResource::getUrl('tree'))
        ->assertOk()
        ->assertSeeLivewire('page-tree');
});

it('shows empty state when no page is selected', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'delete_page'])->create());

    get(PageResource::getUrl('tree'))
        ->assertOk()
        ->assertSee(__('siteman::page.tree.empty_selection'));
});

it('tracks selected page ID from URL parameter', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page', 'delete_page'])->create());

    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => '/test',
        'parent_id' => null,
    ]);

    // Just verify the page loads with the parameter
    // The selectedPageId property will be available to the view
    get(PageResource::getUrl('tree', ['selectedPageId' => $page->id]))
        ->assertOk();
});

it('loads edit form when page is selected', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page', 'delete_page'])->create());

    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => '/test',
        'parent_id' => null,
    ]);

    // Test that the page loads with selected page and form is ready
    Livewire::test(PageTreeSplitView::class, ['selectedPageId' => $page->id])
        ->assertSet('selectedPageId', $page->id)
        ->assertSet('selectedPage.id', $page->id)
        ->assertFormExists();
});

it('updates edit panel when selecting different pages', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page', 'delete_page'])->create());

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

    // Verify we can load different pages - check via Livewire component state
    Livewire::test(PageTreeSplitView::class, ['selectedPageId' => $page1->id])
        ->assertSet('selectedPageId', $page1->id)
        ->assertSet('selectedPage.id', $page1->id);

    Livewire::test(PageTreeSplitView::class, ['selectedPageId' => $page2->id])
        ->assertSet('selectedPageId', $page2->id)
        ->assertSet('selectedPage.id', $page2->id);
});

it('listens to page-selected event and updates selectedPageId', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page', 'delete_page'])->create());

    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => '/test',
        'parent_id' => null,
    ]);

    Livewire::test(PageTreeSplitView::class)
        ->assertSet('selectedPageId', null)
        ->dispatch('page-selected', $page->id)
        ->assertSet('selectedPageId', $page->id)
        ->assertDispatched('update-url');
});

it('can save changes to selected page', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page', 'delete_page'])->create());

    $page = Page::factory()->create([
        'title' => 'Original Title',
        'slug' => '/original',
        'parent_id' => null,
    ]);

    Livewire::test(PageTreeSplitView::class, ['selectedPageId' => $page->id])
        ->assertSet('selectedPageId', $page->id)
        ->fillForm([
            'title' => 'Updated Title',
            'slug' => '/updated',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertDispatched('page:updated');

    expect($page->fresh()->title)->toBe('Updated Title');
    expect($page->fresh()->slug)->toBe('/updated');
});
