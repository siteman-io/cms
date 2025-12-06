<?php declare(strict_types=1);

use Siteman\Cms\Models\Menu;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Menus\LinkTarget;
use Siteman\Cms\Resources\Menus\Livewire\CreateCustomLink;
use Siteman\Cms\Resources\Menus\Livewire\CreateCustomText;
use Siteman\Cms\Resources\Menus\Livewire\CreatePageLink;
use Siteman\Cms\Resources\Menus\Livewire\MenuItems;
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
    $menuItem = $menu->menuItems->first();

    livewire(MenuItems::class, ['menu' => $menu])
        ->callAction('edit', data: [
            'title' => 'updated',
            'url' => 'https://example.com',
            'target' => '_blank',
        ], arguments: ['id' => $menuItem->id, 'title' => $menuItem->title])
        ->assertHasNoActionErrors();

    expect($menu->refresh()->menuItems->first())
        ->title->toBe('updated')
        ->target->toBe('_blank');
});

it('can delete menu items', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->withItems(['https://siteman.io'])->create(['name' => 'Test Menu']);

    livewire(MenuItems::class, ['menu' => $menu])
        ->callAction('delete', arguments: ['id' => $menu->menuItems->first()->id, 'title' => 'siteman'])
        ->assertHasNoActionErrors();

    expect($menu->refresh())->menuItems->toHaveCount(0);
});

it('can create a page link with include_children flag', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->create(['name' => 'Test Menu']);
    $page = Page::factory()->published()->withChildren(2)->create();

    livewire(CreatePageLink::class, ['menu' => $menu])
        ->fillForm([
            'pageId' => $page->id,
            'includeChildren' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $menuItem = $menu->refresh()->menuItems->first();
    expect($menuItem)
        ->linkable_id->toBe($page->id)
        ->include_children->toBeTrue()
        ->getMeta('include_children')->toBeTrue();
});

it('include children toggle is visible only for pages with children', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->create(['name' => 'Test Menu']);
    $pageWithChildren = Page::factory()->published()->withChildren(2)->create();
    $pageWithoutChildren = Page::factory()->published()->create();

    // Toggle should be visible for page with children
    livewire(CreatePageLink::class, ['menu' => $menu])
        ->fillForm(['pageId' => $pageWithChildren->id])
        ->assertFormFieldIsVisible('includeChildren');

    // Toggle should be hidden for page without children
    livewire(CreatePageLink::class, ['menu' => $menu])
        ->fillForm(['pageId' => $pageWithoutChildren->id])
        ->assertFormFieldIsHidden('includeChildren');
});

it('menu item with include_children stores the flag in meta', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->create(['name' => 'Test Menu']);
    $page = Page::factory()->published()->withChildren(3)->create();

    livewire(CreatePageLink::class, ['menu' => $menu])
        ->fillForm([
            'pageId' => $page->id,
            'includeChildren' => true,
        ])
        ->call('save');

    $menuItem = $menu->refresh()->menuItems->first();

    // The include_children flag should be stored in meta
    expect($menuItem->meta)->toBeInstanceOf(\Illuminate\Support\Collection::class);
    expect($menuItem->getMeta('include_children'))->toBeTrue();
    expect($menuItem->include_children)->toBeTrue();

    // The page should have children (which will be rendered dynamically)
    expect($menuItem->linkable->children)->toHaveCount(3);
});

it('children includes page children when include_children is enabled', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->create(['name' => 'Test Menu']);
    $page = Page::factory()->published()->withChildren(3)->create();

    livewire(CreatePageLink::class, ['menu' => $menu])
        ->fillForm([
            'pageId' => $page->id,
            'includeChildren' => true,
        ])
        ->call('save');

    $menuItem = $menu->refresh()->menuItems->first();

    // children should include page children (Page implements MenuItemInterface)
    expect($menuItem->children)->toHaveCount(3);

    // Each child is a Page that implements MenuItemInterface
    $firstChild = $menuItem->children->first();
    expect($firstChild)->toBeInstanceOf(Page::class);
    expect($firstChild->title)->not->toBeNull();
    expect($firstChild->getTitle())->not->toBeNull();
    expect($firstChild->getUrl())->not->toBeNull();
});

it('children returns empty when include_children is disabled', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->create(['name' => 'Test Menu']);
    $page = Page::factory()->published()->withChildren(3)->create();

    // Create without includeChildren
    livewire(CreatePageLink::class, ['menu' => $menu])
        ->fillForm([
            'pageId' => $page->id,
        ])
        ->call('save');

    $menuItem = $menu->refresh()->menuItems->first();

    // children should return empty (no MenuItem children, include_children is false)
    expect($menuItem->children)->toHaveCount(0);
    expect($menuItem->include_children)->toBeFalse();
});

it('can update include_children via edit action', function () {
    actingAs(User::factory()->withPermissions(['view_any_menu', 'update_menu'])->create());
    $menu = Menu::factory()->create(['name' => 'Test Menu']);
    $page = Page::factory()->published()->withChildren(2)->create();

    // Create page link without includeChildren
    livewire(CreatePageLink::class, ['menu' => $menu])
        ->fillForm(['pageId' => $page->id])
        ->call('save');

    $menuItem = $menu->refresh()->menuItems->first();
    expect($menuItem->include_children)->toBeFalse();

    // Update to enable includeChildren
    livewire(MenuItems::class, ['menu' => $menu])
        ->callAction('edit', data: [
            'title' => $menuItem->title,
            'target' => '_self',
            'includeChildren' => true,
        ], arguments: ['id' => $menuItem->id, 'title' => $menuItem->title])
        ->assertHasNoActionErrors();

    expect($menuItem->refresh()->include_children)->toBeTrue();
});
