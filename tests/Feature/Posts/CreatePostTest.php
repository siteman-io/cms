<?php declare(strict_types=1);

use Siteman\Cms\Models\Post;
use Siteman\Cms\Resources\PostResource\Pages\CreatePost;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

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
