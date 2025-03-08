<?php

use Siteman\Cms\Resources\PageResource\Pages\ListPages;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;

it('needs permission to list pages', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(ListPages::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_page'])->create();

    actingAs($user2)
        ->get(ListPages::getUrl())
        ->assertOk();
});
