<?php

namespace Siteman\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Siteman\Cms\Models\Menu;

/** @extends Factory<Menu> */
class MenuFactory extends Factory
{
    protected $model = Menu::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
        ];
    }
}
