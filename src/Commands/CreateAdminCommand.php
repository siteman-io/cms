<?php declare(strict_types=1);

namespace Siteman\Cms\Commands;

use Illuminate\Console\Command;
use Siteman\Cms\Facades\Siteman;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdminCommand extends Command
{
    public $signature = 'siteman:create-admin username {name?} {email?} {password?}';

    public $description = 'This command creates a Siteman admin';

    public function handle(): int
    {
        $username = $this->argument('name') ?? text('Enter the admin user name', default: 'admin');
        $email = $this->argument('email') ?? text('Enter the admin email', default: 'admin@admin.com');
        $password = $this->argument('password') ?? password('Enter the admin password');

        $user = config('siteman.models.user')::factory()->create([
            'name' => $username,
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $user->assignRole(Siteman::createSuperAdminRole());

        $this->components->info('User successfully created. You can now log in at '.Siteman::getLoginUrl());

        return self::SUCCESS;
    }
}
