<?php declare(strict_types=1);

use Siteman\Cms\Resources\Users\Pages\CreateUser;
use Workbench\App\Models\User;

use function Pest\Livewire\livewire;

it('can create a new user', function () {
    $this->actingAs(createUser(permissions: ['view_any_user', 'create_user']));

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
