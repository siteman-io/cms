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
    $page->set('activeTab', 'published');
    $page->assertCountTableRecords(1);
});

it('can filter by author', function () {
    $this->withoutExceptionHandling();
    actingAs(User::factory()->withPermissions(['view_any_post'])->create());

    Post::factory()->count(2)->create();

    $page = livewire(ListPosts::class);

    $page->assertCountTableRecords(2);
    // We use author id 2 since we have created one user to log in
    $page->filterTable('author', [2]);
    $page->assertCountTableRecords(1);
});

it('can list published posts', function () {
    $posts = Post::factory()->count(2)->published()->create();

    $this->get('/blog')
        ->assertOk()
        ->assertSee($posts[0]->title)
        ->assertSee($posts[1]->title);
});
