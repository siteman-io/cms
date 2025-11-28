<?php declare(strict_types=1);

use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\Pages\EditPage;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('needs permission to update pages', function () {
    $user = User::factory()->create();

    $page = Page::factory()->create();
    actingAs($user)
        ->get(EditPage::getUrl([$page]))
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_page', 'update_page'])->create();

    actingAs($user2)
        ->get(EditPage::getUrl([$page]))
        ->assertOk();
});

it('can update pages', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'update_page'])->create());
    $page = Page::factory()->create(['slug' => 'test']);

    livewire(EditPage::class, ['record' => $page->getRouteKey()])
        ->fillForm([
            'title' => 'Test123',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($page->refresh())->title->toBe('Test123');
});
