<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Hash;

test('an unauthenticated user can login', function () {
    $user = createUser(['password' => Hash::make('password')]);

    visit('/admin/login')
        ->type('input[type="email"]', $user->email)
        ->type('input[type="password"]', 'password')
        ->press('Sign in')
        ->assertPathBeginsWith('/admin');
});
