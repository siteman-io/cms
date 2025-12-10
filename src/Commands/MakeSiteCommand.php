<?php declare(strict_types=1);

namespace Siteman\Cms\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Siteman\Cms\Models\Site;

use function Laravel\Prompts\text;

class MakeSiteCommand extends Command
{
    public $signature = 'siteman:make-site {name?} {--slug=} {--domain=}';

    public $description = 'Create a new site';

    public function handle(): int
    {
        $name = $this->argument('name') ?? text(
            label: 'Enter the site name',
            default: config('app.name', 'My Site'),
            required: true,
        );

        $slug = $this->option('slug') ?? Str::slug($name);
        $domain = $this->option('domain') ?: null;

        if (Site::where('slug', $slug)->exists()) {
            $this->components->error("A site with slug '{$slug}' already exists.");

            return self::FAILURE;
        }

        if ($domain && Site::where('domain', $domain)->exists()) {
            $this->components->error("A site with domain '{$domain}' already exists.");

            return self::FAILURE;
        }

        $site = Site::create([
            'name' => $name,
            'slug' => $slug,
            'domain' => $domain,
        ]);

        $this->components->info("Site '{$site->name}' created successfully with slug '{$site->slug}'.");

        return self::SUCCESS;
    }
}
