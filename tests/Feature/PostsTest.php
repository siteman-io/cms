<?php

use Siteman\Cms\Models\Post;
use Siteman\Cms\Resources\PostResource\Pages\CreatePost;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

it('shows only published posts', function () {
    Post::factory()
        ->create([
            'published_at' => null,
            'slug' => 'draft',
        ]);

    Post::factory()
        ->published()
        ->create([
            'slug' => 'published',
        ]);

    $this->get('/blog/draft')->assertStatus(404);
    $this->get('/blog/published')->assertStatus(200);
});

it('needs permission to create posts', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(CreatePost::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_post', 'create_post'])->create();

    actingAs($user2)
        ->get(CreatePost::getUrl())
        ->assertOk();
});

it('can create posts', function () {})->todo();

it('needs permission to update posts', function () {})->todo();
it('can update posts', function () {})->todo();

it('needs permission to delete posts', function () {})->todo();
it('can delete posts', function () {})->todo();

it('properly resizes the featured image', function () {})->todo();
