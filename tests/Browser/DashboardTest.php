<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Hash;

test('it can see the dashboard', function () {
    $this->actingAs(\Workbench\App\Models\User::factory()->create([
        'email' => 'demo@pestphp.com',
        'password' => Hash::make('password'),
    ]));

    visit('/admin')->assertSee('Dashboard')->screenshot();
});
