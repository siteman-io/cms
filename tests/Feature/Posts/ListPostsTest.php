<?php

use Siteman\Cms\Models\Post;
use Siteman\Cms\Resources\PostResource\Pages\ListPosts;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('needs permission to list posts', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(ListPosts::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_post'])->create();

    actingAs($user2)
        ->get(ListPosts::getUrl())
        ->assertOk();
});

it('can filter for published posts', function () {
    actingAs(User::factory()->withPermissions(['view_any_post'])->create());

    Post::factory()->published()->create();
    Post::factory()->create();

    $page = livewire(ListPosts::class);

    $page->assertCountTableRecords(2);
    $page->filterTable('published');
    $page->assertCountTableRecords(1);
});
