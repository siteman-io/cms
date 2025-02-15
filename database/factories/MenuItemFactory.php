<?php

namespace Siteman\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Siteman\Cms\Models\MenuItem;

/** @extends Factory<MenuItem> */
class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'title' => fake()->word(),
        ];
    }
}
