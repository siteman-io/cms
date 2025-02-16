<?php declare(strict_types=1);

use Siteman\Cms\Models\Post;
use Siteman\Cms\Resources\PostResource\Pages\CreatePost;
use Siteman\Cms\Resources\PostResource\Pages\EditPost;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

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

it('can create posts', function () {
    actingAs(User::factory()->withPermissions(['view_any_post', 'create_post'])->create());
    livewire(CreatePost::class)
        ->fillForm([
            'title' => 'Test',
            'slug' => 'test',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Post::whereSlug('test')->exists())->toBeTrue();
});

it('needs permission to update posts', function () {
    $user = User::factory()->create();

    $page = Post::factory()->create();
    actingAs($user)
        ->get(EditPost::getUrl([$page]))
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_post', 'update_post'])->create();

    actingAs($user2)
        ->get(EditPost::getUrl([$page]))
        ->assertOk();
});

it('can update posts', function () {
    actingAs(User::factory()->withPermissions(['view_any_post', 'update_post'])->create());
    $post = Post::factory()->create(['slug' => 'test']);

    livewire(EditPost::class, ['record' => $post->getRouteKey()])
        ->fillForm([
            'title' => 'Test123',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($post->refresh())->title->toBe('Test123');
});

it('needs permission to delete posts', function () {
    actingAs(User::factory()->withPermissions(['view_any_post', 'update_post'])->create());
    $post = Post::factory()->create(['slug' => 'test']);

    livewire(EditPost::class, ['record' => $post->getRouteKey()])
        ->assertActionHidden('delete');
});

it('can delete posts', function () {
    actingAs(User::factory()->withPermissions(['view_any_post', 'update_post', 'delete_post'])->create());
    $post = Post::factory()->create(['slug' => 'test']);

    livewire(EditPost::class, ['record' => $post->getRouteKey()])
        ->callAction('delete');

    expect(Post::count())->toBe(0);
});
