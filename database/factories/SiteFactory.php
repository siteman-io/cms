<?php declare(strict_types=1);

namespace Siteman\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Siteman\Cms\Models\Site;

/** @extends Factory<Site> */
class SiteFactory extends Factory
{
    protected $model = Site::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'slug' => fake()->word(),
            'domain' => fake()->domainName(),
        ];
    }
}
