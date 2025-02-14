<?php

use Siteman\Cms\Resources\MenuResource\Pages\ListMenus;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

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

it('can create menus', function () {})->todo();

it('needs permission to update menus', function () {})->todo();
it('can update menus', function () {})->todo();

it('needs permission to delete menus', function () {})->todo();
it('can delete menus', function () {})->todo();

it('can create menu items', function () {})->todo();

it('can update menu items', function () {})->todo();

it('can delete menu items', function () {})->todo();

it('can update menu location assignments', function () {})->todo();
