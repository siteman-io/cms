<?php declare(strict_types=1);

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Resources\Menus\Pages\ListMenus;

use function Pest\Livewire\livewire;

it('needs permission to list menus', function () {
    $this->actingAs(createUser());
    $site = Siteman::getCurrentSite();

    $this->get(ListMenus::getUrl(tenant: $site))
        ->assertForbidden();

    $this->actingAs(createUser(permissions: ['view_any_menu']));

    $this->get(ListMenus::getUrl(tenant: $site))
        ->assertOk();
});

it('can update menu location assignments', function () {
    $this->actingAs(createUser(permissions: ['view_any_menu', 'create_menu']));

    $menu = Menu::factory()->create();
    livewire(ListMenus::class)
        ->callAction('locations', ['header' => ['menu' => $menu->id]])
        ->assertHasNoActionErrors();

    expect($menu->refresh())->locations->toHaveCount(1);
});
