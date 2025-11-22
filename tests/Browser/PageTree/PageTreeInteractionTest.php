<?php declare(strict_types=1);

use Livewire\Livewire;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\Livewire\PageTree as PageTreeComponent;
use Siteman\Cms\Resources\Pages\PageResource;
use Siteman\Cms\Resources\Pages\Pages\PageTree;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page'])->create());
});

it('dispatches page-selected event when tree item is clicked', function () {
    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => '/test',
        'parent_id' => null,
    ]);

    Livewire::test(PageTreeComponent::class)
        ->call('selectPage', $page->id)
        ->assertDispatched('page-selected', $page->id);
});

it('page tree component receives page-selected event and updates view', function () {
    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => '/test',
        'parent_id' => null,
    ]);

    // The PageTree page component listens to page-selected and updates selectedPageId
    Livewire::test(PageTree::class)
        ->assertSet('selectedPageId', null)
        ->dispatch('page-selected', $page->id)
        ->assertSet('selectedPageId', $page->id);
});

it('clicking tree item in split view updates URL parameter', function () {
    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => '/test',
        'parent_id' => null,
    ]);

    // Test the full flow: load page with URL parameter
    $this->get(PageResource::getUrl('tree', ['selectedPageId' => $page->id]))
        ->assertOk()
        ->assertSee($page->title);
});
