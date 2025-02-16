<?php declare(strict_types=1);

use Siteman\Cms\Resources\UserResource\Pages\CreateUser;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('can create a new user', function () {
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
})->todo('We need to make an invitation out of the user creation process');
