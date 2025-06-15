<?php declare(strict_types=1);

use Siteman\Cms\Models\Menu;
use Siteman\Cms\Resources\Menus\Pages\EditMenu;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('needs permission to update menus', function () {
    $user = User::factory()->create();
    $menu = Menu::factory()->create();

    actingAs($user)
        ->get(EditMenu::getUrl([$menu]))
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create();

    actingAs($user2)
        ->get(EditMenu::getUrl([$menu]))
        ->assertOk();
});

it('can update menus', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->create(['name' => 'Old Name']);

    livewire(EditMenu::class, ['record' => $menu->id])
        ->fillForm(['name' => 'New Name'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($menu->refresh())->name->toBe('New Name');
});
