<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\Pages\CreatePage;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('needs permission to create pages', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(CreatePage::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_page', 'create_page'])->create();

    actingAs($user2)
        ->get(CreatePage::getUrl())
        ->assertOk();
});

it('can create pages', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'create_page'])->create());

    livewire(CreatePage::class)
        ->fillForm([
            'title' => 'Test',
            'slug' => '/test',
            'type' => 'page',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Page::whereSlug('/test')->exists())->toBeTrue();
});
