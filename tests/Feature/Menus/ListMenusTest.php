<?php declare(strict_types=1);

use Siteman\Cms\Models\Menu;
use Siteman\Cms\Resources\MenuResource\Pages\ListMenus;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('needs permission to list menus', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(ListMenus::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions('view_any_menu')->create();

    actingAs($user2)
        ->get(ListMenus::getUrl())
        ->assertOk();
});

it('can update menu location assignments', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'create_menu'])->create());

    $menu = Menu::factory()->create();
    livewire(ListMenus::class)
        ->callAction('locations', ['header' => ['menu' => $menu->id]])
        ->assertHasNoActionErrors();

    expect($menu->refresh())->locations->toHaveCount(1);
});
