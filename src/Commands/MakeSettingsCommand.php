<?php

declare(strict_types=1);

namespace Siteman\Cms\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Siteman\Cms\Commands\Generator\SettingsGenerator;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:siteman-settings')]
class MakeSettingsCommand extends Command
{
    public $signature = 'make:siteman-settings {name?}';

    public $description = 'Create Siteman SettingsForm besides Settings class and migration';

    public function handle(SettingsGenerator $generator): int
    {
        $name = (string) str(
            $this->argument('name') ??
            text(
                label: 'What is the Settings name?',
                placeholder: 'ThemeSettings',
                required: true,
            ),
        )
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $settingsClass = (string) str($name)->afterLast('\\');
        $settingsNamespace = str($name)->contains('\\')
            ? (string) str($name)->beforeLast('\\')
            : app()->getNamespace().'Settings';

        $group = (string) str($settingsClass)->lower()->before('settings');

        $settingsPath = $this->getClassPath($settingsNamespace, $settingsClass);
        File::ensureDirectoryExists(dirname($settingsPath));
        File::put($settingsPath, $generator->generateSettings($settingsClass, $settingsNamespace, $group));

        $this->components->info('Settings class created successfully.');

        $settingsFormClass = $settingsClass.'Form';
        $settingsFormPath = $this->getClassPath($settingsNamespace, $settingsFormClass);
        File::put(
            $settingsFormPath,
            $generator->generateSettingsForm($settingsFormClass, $settingsNamespace, $settingsClass, $settingsNamespace)
        );

        $this->components->info('Settings Form class successfully created!');

        $path = $this->resolveMigrationPaths()[0];
        $migrationName = 'Create'.$settingsClass;

        $this->ensureMigrationDoesntAlreadyExist($migrationName, $path);

        File::ensureDirectoryExists($path);
        $migrationFile = $this->getPath($migrationName, $path);
        File::put($migrationFile, $generator->generateMigration($group));

        $this->components->info(sprintf('Setting migration [%s] created successfully.', $migrationFile));
        $this->components->info('Remember to register the SettingsForm via the configure method in your Theme');

        return self::SUCCESS;
    }

    protected function ensureMigrationDoesntAlreadyExist(string $name, ?string $migrationPath = null): void
    {
        if (!empty($migrationPath)) {
            $migrationFiles = File::glob($migrationPath.'/*.php');

            foreach ($migrationFiles as $migrationFile) {
                File::requireOnce($migrationFile);
            }
        }

        if (class_exists($className = Str::studly($name))) {
            throw new InvalidArgumentException("A {$className} class already exists.");
        }
    }

    protected function getPath(string $name, string $path): string
    {
        return $path.'/'.Carbon::now()->format('Y_m_d_His').'_'.Str::snake($name).'.php';
    }

    /**
     * @return array<string>
     */
    protected function resolveMigrationPaths(): array
    {
        return !empty(config('settings.migrations_path'))
            ? [config('settings.migrations_path')]
            : config('settings.migrations_paths');
    }

    private function getClassPath(string $namespace, string $class): string
    {
        $appNamespace = trim(app()->getNamespace(), '\\');

        if (str_starts_with($namespace, $appNamespace)) {
            $relativePath = str($namespace)
                ->after($appNamespace)
                ->trim('\\')
                ->replace('\\', '/');

            return app_path($relativePath.'/'.$class.'.php');
        }

        return base_path(str($namespace)->replace('\\', '/').'/'.$class.'.php');
    }
}
