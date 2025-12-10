<?php declare(strict_types=1);

use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Resources\Roles\Pages\EditRole;

it('needs permission to edit a role', function () {
    $this->actingAs(createUser());
    $site = Siteman::getCurrentSite();
    $role = Siteman::createRole('test');

    $this->get(EditRole::getUrl([$role], tenant: $site))
        ->assertForbidden();

    $this->actingAs(createUser(permissions: ['view_any_role', 'update_role']));

    $this->get(EditRole::getUrl([$role], tenant: $site))
        ->assertOk();
});
