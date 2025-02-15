<?php

namespace Siteman\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Models\MenuItem;

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

    public function withItems(array $items): static
    {
        return $this->has(MenuItem::factory()
            ->count(count($items))
            ->state(new Sequence(
                fn (Sequence $sequence) => ['title' => $items[$sequence->index]],
            )),

            'menuItems');
    }
}
