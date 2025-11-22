<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\Livewire\PageTree;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    actingAs(User::factory()->create());
});

it('loads root pages with descendants', function () {
    // Create a tree structure:
    // Root 1
    //   ├─ Child 1.1
    //   └─ Child 1.2
    // Root 2
    //   └─ Child 2.1
    //       └─ Grandchild 2.1.1

    $root1 = Page::factory()->create([
        'title' => 'Root 1',
        'slug' => '/root-1',
        'parent_id' => null,
    ]);

    $child11 = Page::factory()->create([
        'title' => 'Child 1.1',
        'slug' => '/child-1-1',
        'parent_id' => $root1->id,
    ]);

    $child12 = Page::factory()->create([
        'title' => 'Child 1.2',
        'slug' => '/child-1-2',
        'parent_id' => $root1->id,
    ]);

    $root2 = Page::factory()->create([
        'title' => 'Root 2',
        'slug' => '/root-2',
        'parent_id' => null,
    ]);

    $child21 = Page::factory()->create([
        'title' => 'Child 2.1',
        'slug' => '/child-2-1',
        'parent_id' => $root2->id,
    ]);

    $grandchild211 = Page::factory()->create([
        'title' => 'Grandchild 2.1.1',
        'slug' => '/grandchild-2-1-1',
        'parent_id' => $child21->id,
    ]);

    $component = livewire(PageTree::class);

    // Component should load successfully
    $component->assertOk();

    // Get the pages from the component
    $pages = $component->get('pages');

    // Should have 2 root pages
    expect($pages)->toHaveCount(2);

    // Check first root and its children
    $firstRoot = $pages->first();
    expect($firstRoot->id)->toBe($root1->id);
    expect($firstRoot->children)->toHaveCount(2);

    // Check second root and its nested structure
    $secondRoot = $pages->last();
    expect($secondRoot->id)->toBe($root2->id);
    expect($secondRoot->children)->toHaveCount(1);

    // Check if grandchildren are loaded
    $child = $secondRoot->children->first();
    expect($child->id)->toBe($child21->id);

    // Verify grandchildren exist (this might fail if not loaded)
    expect($child->children)->not->toBeNull();
    expect($child->children)->toHaveCount(1);
    expect($child->children->first()->id)->toBe($grandchild211->id);
});

it('dispatches page-selected event when selectPage is called', function () {
    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => '/test',
        'parent_id' => null,
    ]);

    livewire(PageTree::class)
        ->call('selectPage', $page->id)
        ->assertDispatched('page-selected', $page->id);
});

it('refreshes when page:deleted event is received', function () {
    $page1 = Page::factory()->create([
        'title' => 'Page 1',
        'slug' => '/page-1',
        'parent_id' => null,
    ]);

    $page2 = Page::factory()->create([
        'title' => 'Page 2',
        'slug' => '/page-2',
        'parent_id' => null,
    ]);

    $component = livewire(PageTree::class);

    // Initially should have 2 pages
    expect($component->get('pages'))->toHaveCount(2);

    // Delete one page
    $page1->delete();

    // Dispatch the page:deleted event
    $component->dispatch('page:deleted', $page1->id);

    // Component should refresh and now show only 1 page
    expect($component->get('pages'))->toHaveCount(1);
    expect($component->get('pages')->first()->id)->toBe($page2->id);
});
