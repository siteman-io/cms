<?php

namespace Siteman\Cms\Commands;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    public $signature = 'siteman:publish';

    public $description = 'Publish migrations and assets for Siteman and its dependencies';

    public function handle(): int
    {
        $this->call('vendor:publish', ['--tag' => [
            'migrations', // Publish spatie/laravel-settings and overtrue/versionable migrations
            'health-migrations', // Publish spatie/laravel-health migrations
            'permission-migrations', // Publish spatie/laravel-permission migrations
            'medialibrary-migrations', // publish migrations form spatie/laravel-medialibrary
            'tags-migrations', // Publish spatie/laravel-tags migrations
            'seo-migrations', // Publish ralphjsmit/laravel-seo migrations
            'feed-assets', // Publish spatie/laravel-feed assets
            'siteman-migrations',
            'siteman-config',
        ]]);

        return self::SUCCESS;
    }
}
