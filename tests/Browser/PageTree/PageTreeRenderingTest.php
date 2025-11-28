<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\PageResource;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'delete_page'])->create());
});

it('renders tree with root and nested pages', function () {
    $root1 = Page::factory()
        ->withChildren([['slug' => '/child-1'], ['slug' => '/child-2']])
        ->create(['slug' => '/root-1']);
    $root2 = Page::factory()->create(['slug' => '/root-2']);

    visit(PageResource::getUrl('tree'))
        ->assertSee($root1->slug)
        ->assertSee($root2->slug)
        ->click('[data-sortable-item="'.$root1->id.'"] > div > div:first-child button[title="Expand"]')
        ->assertSee('/child-1')
        ->assertSee('/child-2');
});

it('shows page type badges in tree', function () {
    $page = Page::factory()->create(['slug' => '/test', 'type' => 'page']);
    $internalPage = Page::factory()->create(['slug' => '/internal', 'type' => 'internal']);

    visit(PageResource::getUrl('tree'))
        ->assertSee($page->slug)
        ->assertSee($internalPage->slug)
        ->assertSee('page')
        ->assertSee('internal');
});
