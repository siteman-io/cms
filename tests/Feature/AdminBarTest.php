<?php

use Workbench\App\Models\User;
use Workbench\Database\Seeders\DatabaseSeeder;

use function Pest\Laravel\seed;

it('gets injected when logged in', function () {
    seed(DatabaseSeeder::class);

    // Use the user created by the seeder since it sets up the site properly
    $user = User::where('email', 'admin@admin.com')->first();

    $this->actingAs($user)->get('/')
        ->assertOk()
        ->assertSeeHtml('id="adminBar"');
});
