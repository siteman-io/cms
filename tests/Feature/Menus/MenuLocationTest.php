<?php declare(strict_types=1);

use Siteman\Cms\Models\Menu;
use Siteman\Cms\Resources\Menus\Pages\ListMenus;

use function Pest\Livewire\livewire;

it('can update menu location assignments', function () {
    $this->actingAs(createUser(permissions: ['view_any_menu', 'create_menu']));

    $menu = Menu::factory()->create();
    livewire(ListMenus::class)
        ->callAction('locations', ['header' => ['menu' => $menu->id]])
        ->assertHasNoActionErrors();

    expect($menu->refresh())->locations->toHaveCount(1);
});
