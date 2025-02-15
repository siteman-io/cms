<?php

use Siteman\Cms\Models\Menu;
use Siteman\Cms\Resources\MenuResource\Pages\EditMenu;
use Siteman\Cms\Resources\MenuResource\Pages\ListMenus;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('needs permission to view menus', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(ListMenus::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions('view_any_menu')->create();

    actingAs($user2)
        ->get(ListMenus::getUrl())
        ->assertOk();
});

it('can create menus', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'create_menu'])->create());

    livewire(ListMenus::class)
        ->callAction('create', ['name' => 'Test Menu'])
        ->assertHasNoActionErrors();

    expect(Menu::where('name', 'Test Menu')->exists())->toBeTrue();
});

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

it('needs permission to delete menus', function () {})->todo();
it('can delete menus', function () {})->todo();

it('can create menu items', function () {})->todo();

it('can update menu items', function () {})->todo();

it('can delete menu items', function () {})->todo();

it('can update menu location assignments', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'create_menu'])->create());

    $menu = Menu::factory()->create();
    livewire(ListMenus::class)
        ->callAction('locations', ['header' => ['menu' => $menu->id]])
        ->assertHasNoActionErrors();

    expect($menu->refresh())->locations->toHaveCount(1);
});
