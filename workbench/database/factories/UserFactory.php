<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Siteman\Cms\Facades\Siteman;
use Workbench\App\Models\User;

/**
 * @template TModel of \Workbench\App\Models\User
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function isSuperAdmin(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(Siteman::createSuperAdminRole());
            $user->refresh();
        });
    }

    public function withPermissions(string|array $permissions): static
    {
        return $this->afterCreating(function (User $user) use ($permissions) {
            $createdPermissions = collect($permissions)->map(fn ($permission) => Siteman::getPermissionModel()::firstOrCreate(['name' => $permission, 'guard' => 'web']));
            $user->givePermissionTo($createdPermissions);
            $user->refresh();
        });
    }
}
