<?php

use Siteman\Cms\Resources\UserResource\Pages\CreateUser;
use Siteman\Cms\Resources\UserResource\Pages\ListUsers;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('needs permission to view users page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(ListUsers::getUrl())
        ->assertForbidden();

    $user2 = User::factory()->withPermissions(['view_any_user'])->create();

    actingAs($user2)
        ->get(ListUsers::getUrl())
        ->assertOk();
});

it('can create users', function () {
    actingAs(User::factory()->withPermissions(['view_any_user', 'create_user'])->create());

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'test user',
            'email' => 'test@example.com',
            'password' => 'password',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});
