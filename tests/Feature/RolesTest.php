<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages\CreateRole;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages\EditRole;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages\ListRoles;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

it('needs permission to view the roles', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(ListRoles::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions('view_any_role')->create();

    actingAs($user2)
        ->get(ListRoles::getUrl())
        ->assertOk();
});

it('needs permission to create a role', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(CreateRole::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_role', 'create_role'])->create();

    actingAs($user2)
        ->get(CreateRole::getUrl())
        ->assertOk();
});

it('needs permission to edit a role', function () {
    $user = User::factory()->create();

    $role = FilamentShield::createRole('test');

    actingAs($user)
        ->get(EditRole::getUrl([$role]))
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_role', 'update_role'])->create();

    actingAs($user2)
        ->get(EditRole::getUrl([$role]))
        ->assertOk();
});
