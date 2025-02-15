<?php declare(strict_types=1);

use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages\ListRoles;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

it('needs permission to list roles', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(ListRoles::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions('view_any_role')->create();

    actingAs($user2)
        ->get(ListRoles::getUrl())
        ->assertOk();
});
