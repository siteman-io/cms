<?php declare(strict_types=1);

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Resources\Roles\Pages\EditRole;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

it('needs permission to edit a role', function () {
    $user = User::factory()->create();

    $role = Siteman::createRole('test');

    actingAs($user)
        ->get(EditRole::getUrl([$role]))
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_role', 'update_role'])->create();

    actingAs($user2)
        ->get(EditRole::getUrl([$role]))
        ->assertOk();
});
