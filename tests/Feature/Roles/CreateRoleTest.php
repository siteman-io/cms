<?php declare(strict_types=1);

use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages\CreateRole;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

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
