<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\PageResource;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->withPermissions(['view_any_page'])->create());
});

it('renders tree structure with nested pages', function () {
    // Create a tree structure:
    // Root 1
    //   ├─ Child 1.1
    //   └─ Child 1.2
    // Root 2

    $root1 = Page::factory()->create([
        'title' => 'Root Page One',
        'slug' => '/root-1',
        'parent_id' => null,
    ]);

    $child11 = Page::factory()->create([
        'title' => 'Child One One',
        'slug' => '/child-1-1',
        'parent_id' => $root1->id,
    ]);

    $child12 = Page::factory()->create([
        'title' => 'Child One Two',
        'slug' => '/child-1-2',
        'parent_id' => $root1->id,
    ]);

    $root2 = Page::factory()->create([
        'title' => 'Root Page Two',
        'slug' => '/root-2',
        'parent_id' => null,
    ]);

    $this->get(PageResource::getUrl('tree'))
        ->assertOk()
        ->assertSeeLivewire('page-tree')
        // Check slugs are visible (tree shows slugs, not titles)
        ->assertSee('/root-1')
        ->assertSee('/root-2')
        ->assertSee('/child-1-1')
        ->assertSee('/child-1-2');
});

it('shows page type badges in tree', function () {
    $page = Page::factory()->create([
        'title' => 'Test Page',
        'slug' => '/test',
        'type' => 'page',
        'parent_id' => null,
    ]);

    $internalPage = Page::factory()->create([
        'title' => 'Internal Page',
        'slug' => '/internal',
        'type' => 'internal',
        'parent_id' => null,
    ]);

    $this->get(PageResource::getUrl('tree'))
        ->assertOk()
        // Check slugs are visible
        ->assertSee('/test')
        ->assertSee('/internal')
        // Check type badges are visible
        ->assertSee('page')
        ->assertSee('internal');
});

it('displays empty state when no pages exist', function () {
    $this->get(PageResource::getUrl('tree'))
        ->assertOk()
        ->assertSee('No pages found');
});
