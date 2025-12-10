<?php declare(strict_types=1);

use Siteman\Cms\Models\Menu;
use Siteman\Cms\Resources\Menus\Pages\EditMenu;

use function Pest\Livewire\livewire;

it('needs permission to delete menus', function () {
    $this->actingAs(createUser(permissions: ['view_any_menu', 'update_menu']));
    $menu = Menu::factory()->create(['name' => 'Old Name']);

    livewire(EditMenu::class, ['record' => $menu->id])
        ->assertActionHidden('delete');
});

it('can delete menus', function () {
    $this->actingAs(createUser(permissions: ['view_any_menu', 'update_menu', 'delete_menu']));
    $menu = Menu::factory()->create(['name' => 'Old Name']);

    livewire(EditMenu::class, ['record' => $menu->id])
        ->callAction('delete')
        ->assertHasNoActionErrors();

    expect(Menu::count())->toBe(0);
});
