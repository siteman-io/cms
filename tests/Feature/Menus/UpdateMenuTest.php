<?php declare(strict_types=1);

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Resources\Menus\Pages\EditMenu;

use function Pest\Livewire\livewire;

it('needs permission to update menus', function () {
    $this->actingAs(createUser());
    $site = Siteman::getCurrentSite();
    $menu = Menu::factory()->create();

    $this->get(EditMenu::getUrl([$menu], tenant: $site))
        ->assertForbidden();

    $this->actingAs(createUser(permissions: ['view_any_menu', 'update_menu']));

    $this->get(EditMenu::getUrl([$menu], tenant: $site))
        ->assertOk();
});

it('can update menus', function () {
    $this->actingAs(createUser(permissions: ['view_any_menu', 'update_menu']));
    $menu = Menu::factory()->create(['name' => 'Old Name']);

    livewire(EditMenu::class, ['record' => $menu->id])
        ->fillForm(['name' => 'New Name'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($menu->refresh())->name->toBe('New Name');
});
