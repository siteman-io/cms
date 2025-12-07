<?php declare(strict_types=1);

namespace Siteman\Cms\Commands;

use Illuminate\Console\Command;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Site;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class CreateAdminCommand extends Command
{
    public $signature = 'siteman:create-admin {name?} {email?} {password?} {--site= : Site ID or slug}';

    public $description = 'This command creates a Siteman admin for a site';

    public function handle(): int
    {
        $site = $this->resolveSite();

        if (!$site) {
            $this->components->error('No site found. Please create a site first or specify a valid --site option.');

            return self::FAILURE;
        }

        Siteman::setCurrentSite($site);

        $username = $this->argument('name') ?? text('Enter the admin user name', default: 'admin');
        $email = $this->argument('email') ?? text('Enter the admin email', default: 'admin@admin.com');
        $password = $this->argument('password') ?? password('Enter the admin password');

        $user = config('siteman.models.user')::factory()->create([
            'name' => $username,
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $user->sites()->attach($site);
        $user->assignRole(Siteman::createSuperAdminRole());

        $this->components->info("User successfully created for site '{$site->name}'. You can now log in at ".Siteman::getLoginUrl());

        return self::SUCCESS;
    }

    protected function resolveSite(): ?Site
    {
        $siteOption = $this->option('site');

        if ($siteOption) {
            return Site::where('id', $siteOption)
                ->orWhere('slug', $siteOption)
                ->orWhere('domain', $siteOption)
                ->first();
        }

        $sites = Site::all();

        if ($sites->isEmpty()) {
            return null;
        }

        if ($sites->count() === 1) {
            return $sites->first();
        }

        $siteId = select(
            label: 'Select a site for the admin user',
            options: $sites->pluck('name', 'id')->toArray(),
        );

        return Site::find($siteId);
    }
}
