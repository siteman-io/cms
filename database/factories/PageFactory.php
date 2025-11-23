<?php

namespace Siteman\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        $title = fake()->unique()->sentence(rand(1, 3));

        return [
            'author_id' => $this->getUserFactory(),
            'title' => $title,
            'slug' => '/'.Str::slug($title),
            'blocks' => [],
        ];
    }

    public function withChildren(int|array|PageFactory $children = 2): static
    {
        if (is_int($children)) {
            $children = Page::factory($children);
        }
        if (is_array($children)) {
            $children = Page::factory()->forEachSequence(...$children);
        }

        return $this->has($children, 'children');
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

    protected function getUserFactory(): Factory
    {
        $userModel = config('siteman.models.user');
        if (!class_uses($userModel, HasFactory::class)) {
            throw new \Exception('User model must use the HasFactory trait and implement Authenticatable.');
        }

        return $userModel::factory();
    }
}
