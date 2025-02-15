<?php

use Workbench\App\Models\User;
use Workbench\Database\Seeders\DatabaseSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

it('gets injected when logged in', function () {
    seed(DatabaseSeeder::class);

    actingAs(User::factory()->create())->get('/')
        ->assertOk()
        ->assertSeeHtml('id="adminBar"');
});
