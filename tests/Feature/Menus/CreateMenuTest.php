<?php declare(strict_types=1);

use Siteman\Cms\Models\Menu;
use Siteman\Cms\Resources\MenuResource\Pages\ListMenus;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('needs permission to create menus', function () {
    actingAs(User::factory()->withPermissions('view_any_menu')->create());

    livewire(ListMenus::class)->assertActionHidden('create');
});

it('can create menus', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'create_menu'])->create());

    livewire(ListMenus::class)
        ->callAction('create', ['name' => 'Test Menu'])
        ->assertHasNoActionErrors();

    expect(Menu::where('name', 'Test Menu')->exists())->toBeTrue();
});
