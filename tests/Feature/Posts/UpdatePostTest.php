<?php declare(strict_types=1);

use Siteman\Cms\Models\Post;
use Siteman\Cms\Resources\PostResource\Pages\EditPost;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

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
