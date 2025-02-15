<?php

use Siteman\Cms\Models\Menu;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\MenuResource\LinkTarget;
use Siteman\Cms\Resources\MenuResource\Livewire\CreateCustomLink;
use Siteman\Cms\Resources\MenuResource\Livewire\CreateCustomText;
use Siteman\Cms\Resources\MenuResource\Livewire\CreatePageLink;
use Siteman\Cms\Resources\MenuResource\Livewire\MenuItems;
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

it('needs permission to delete menus', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->create(['name' => 'Old Name']);

    livewire(EditMenu::class, ['record' => $menu->id])
        ->assertActionHidden('delete');
});

it('can delete menus', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu', 'delete_menu'])->create());
    $menu = Menu::factory()->create(['name' => 'Old Name']);

    livewire(EditMenu::class, ['record' => $menu->id])
        ->callAction('delete')
        ->assertHasNoActionErrors();

    expect(Menu::count())->toBe(0);
});

it('can create a page link as a menu item', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->create(['name' => 'Test Menu']);
    $page = Page::factory()->create();

    livewire(CreatePageLink::class, ['menu' => $menu])
        ->fillForm([
            'pageId' => $page->id,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($menu->refresh()->menuItems->first()->linkable_id)->toBe($page->id);
});

it('can create a custom link as a menu item', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->create(['name' => 'Test Menu']);

    livewire(CreateCustomLink::class, ['menu' => $menu])
        ->fillForm([
            'title' => 'foo',
            'url' => 'https://siteman.io',
            'target' => LinkTarget::Self->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($menu->refresh())->menuItems->toHaveCount(1);
});

it('can create a custom text as a menu item', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->create(['name' => 'Test Menu']);

    livewire(CreateCustomText::class, ['menu' => $menu])
        ->fillForm([
            'title' => 'foo',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($menu->refresh())->menuItems->toHaveCount(1);
});

it('can update menu items', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->withItems(['https://siteman.io'])->create(['name' => 'Test Menu']);

    livewire(MenuItems::class, ['menu' => $menu])
        ->callAction('editAction', ['title' => 'updated'], ['id' => $menu->menuItems->first()->id, 'title' => 'siteman'])
        ->assertHasNoActionErrors();

    expect($menu->refresh())->menuItems->first()->title->toBe('updated');
});

it('can delete menu items', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->withItems(['https://siteman.io'])->create(['name' => 'Test Menu']);

    livewire(MenuItems::class, ['menu' => $menu])
        ->callAction('deleteAction', arguments: ['id' => $menu->menuItems->first()->id, 'title' => 'siteman'])
        ->assertHasNoActionErrors();

    expect($menu->refresh())->menuItems->toHaveCount(0);
});

it('can update menu location assignments', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'create_menu'])->create());

    $menu = Menu::factory()->create();
    livewire(ListMenus::class)
        ->callAction('locations', ['header' => ['menu' => $menu->id]])
        ->assertHasNoActionErrors();

    expect($menu->refresh())->locations->toHaveCount(1);
});
