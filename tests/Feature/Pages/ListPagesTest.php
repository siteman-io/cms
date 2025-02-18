<?php

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\PageResource\Pages\ListPages;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('needs permission to list pages', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(ListPages::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_page'])->create();

    actingAs($user2)
        ->get(ListPages::getUrl())
        ->assertOk();
});

it('can filter for published pages', function () {
    actingAs(User::factory()->withPermissions(['view_any_page'])->create());

    Page::factory()->published()->create();
    Page::factory()->create();

    $page = livewire(ListPages::class);

    $page->assertCountTableRecords(2);
    $page->set('activeTab', 'published');
    $page->assertCountTableRecords(1);
});
