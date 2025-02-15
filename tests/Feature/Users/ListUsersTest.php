<?php declare(strict_types=1);

use Siteman\Cms\Resources\UserResource\Pages\ListUsers;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

it('needs permission to list users', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(ListUsers::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_user'])->create();

    actingAs($user2)
        ->get(ListUsers::getUrl())
        ->assertOk();
});
