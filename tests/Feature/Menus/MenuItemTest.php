<?php declare(strict_types=1);

use Siteman\Cms\Models\Menu;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\MenuResource\LinkTarget;
use Siteman\Cms\Resources\MenuResource\Livewire\CreateCustomLink;
use Siteman\Cms\Resources\MenuResource\Livewire\CreateCustomText;
use Siteman\Cms\Resources\MenuResource\Livewire\CreatePageLink;
use Siteman\Cms\Resources\MenuResource\Livewire\MenuItems;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

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
