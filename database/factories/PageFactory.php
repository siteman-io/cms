<?php

namespace Siteman\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Siteman\Cms\Models\Page;

/** @extends Factory<Page> */
class PageFactory extends Factory
{
    use GeneratesBlocks;

    protected $model = Page::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(rand(2, 3));

        return [
            'author_id' => config('siteman.models.user')::factory(),
            'title' => $title,
            'slug' => '/'.Str::slug($title),
            'blocks' => [],
        ];
    }

    public function published(?Carbon $publishedAt = null): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => $publishedAt ?? now()->subDays(rand(0, 365)),
        ]);
    }

    public function withTags(array $tags): static
    {
        return $this->afterCreating(function (Page $page) use ($tags) {
            $page->attachTags($tags);
        });
    }
}
