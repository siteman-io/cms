<?php declare(strict_types=1);

use Siteman\Cms\Models\Menu;
use Siteman\Cms\Resources\Menus\Pages\ListMenus;

use function Pest\Livewire\livewire;

it('needs permission to create menus', function () {
    $this->actingAs(createUser(permissions: ['view_any_menu']));

    livewire(ListMenus::class)->assertActionHidden('create');
});

it('can create menus', function () {
    $this->actingAs(createUser(permissions: ['view_any_menu', 'create_menu']));

    livewire(ListMenus::class)
        ->callAction('create', ['name' => 'Test Menu'])
        ->assertHasNoActionErrors();

    expect(Menu::where('name', 'Test Menu')->exists())->toBeTrue();
});
