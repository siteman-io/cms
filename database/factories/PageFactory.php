<?php

namespace Siteman\Cms\Database\Factories;

use Siteman\Cms\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/** @extends Factory<Page> */
class PageFactory extends Factory
{
    use GeneratesBlocks;

    protected $model = Page::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $title = fake()->unique()->sentence;

        return [
            'author_id' => config('siteman.models.user')::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'blocks' => $this->makeBlocks(rand(1, 3)),
        ];
    }

    public function published(?Carbon $publishedAt = null): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => $publishedAt ?? now()->subDays(rand(0, 365)),
        ]);
    }
}
