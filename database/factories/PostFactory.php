<?php

namespace Siteman\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Siteman\Cms\Models\Post;

/** @extends Factory<Post> */
class PostFactory extends Factory
{
    use GeneratesBlocks;

    protected $model = Post::class;

    /** @return array<string, mixed> */
    public function definition()
    {
        $title = fake()->unique()->sentence;

        return [
            'author_id' => config('siteman.models.user')::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'blocks' => [],
            'published_at' => null,
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
        return $this->afterCreating(function (Post $post) use ($tags) {
            $post->attachTags($tags);
        });
    }
}
