<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\PageResource;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page', 'delete_page'])->create());
});

it('displays pages in hierarchical order', function () {
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

it('reflects hierarchy changes after moving page to different parent', function () {
    $parent = Page::factory()->create(['slug' => '/parent']);
    $page = Page::factory()->create(['slug' => '/standalone']);

    visit(PageResource::getUrl())
        ->assertSee($parent->slug)
        ->assertSee($page->slug);

    $page->update(['parent_id' => $parent->id]);

    visit(PageResource::getUrl())
        ->assertSee($parent->slug)
        ->click('[data-page-expand="'.$parent->id.'"]')
        ->assertSee($page->slug);
});

it('shows page at root level after removing parent', function () {
    $parent = Page::factory()
        ->withChildren(Page::factory()->state(['slug' => '/child']))
        ->create(['slug' => '/parent']);
    $child = $parent->children->first();

    visit(PageResource::getUrl())
        ->assertSee($parent->slug)
        ->click('[data-page-expand="'.$parent->id.'"]')
        ->assertSee($child->slug);

    $child->update(['parent_id' => null]);

    visit(PageResource::getUrl())
        ->assertSee($parent->slug)
        ->assertSee($child->slug);
});
