<?php

namespace Siteman\Cms\Commands;

use Carbon\Carbon;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:siteman-settings')]
class MakeSettingsCommand extends Command
{
    use CanManipulateFiles;

    public $signature = 'make:siteman-settings {name?}';

    public $description = 'Create Siteman SettingsForm besides Settings class and migration';

    public function handle(): int
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
        $settingsNamespace = str($name)->contains('\\') ?
            (string) str($name)->beforeLast('\\') :
            app()->getNamespace().'Settings';

        $this->copyStubToApp('Settings', base_path(str($settingsNamespace)->replace('\\', '/').'/'.$settingsClass.'.php'), [
            'namespace' => $settingsNamespace,
            'class' => $settingsClass,
            'group' => str($settingsClass)->lower()->before('settings'),
        ]);

        $this->components->info('Settings class created successfully.');

        $settingsFormClass = $settingsClass.'Form';

        $this->copyStubToApp('SettingsForm', base_path(str($settingsNamespace)->replace('\\', '/').'/'.$settingsFormClass.'.php'), [
            'namespace' => $settingsNamespace,
            'class' => $settingsFormClass,
            'settingsClass' => '\\'.$settingsNamespace.'\\'.$settingsClass,
        ]);

        $this->components->info('Settings Form class successfully created!');

        $path = $this->resolveMigrationPaths()[0];

        $migrationName = 'Create'.$settingsClass;
        $this->ensureMigrationDoesntAlreadyExist($migrationName, $path);

        File::ensureDirectoryExists($path);

        $this->copyStubToApp('SettingsMigration', $file = $this->getPath($migrationName, $path), [
            'group' => str($settingsClass)->lower()->before('settings'),
        ]);

        $this->components->info(sprintf('Setting migration [%s] created successfully.', $file));

        $this->components->info('Remember to register the SettingsForm via the configure method in your Theme');

        return self::SUCCESS;
    }

    protected function ensureMigrationDoesntAlreadyExist($name, $migrationPath = null): void
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

    protected function getPath($name, $path): string
    {
        return $path.'/'.Carbon::now()->format('Y_m_d_His').'_'.Str::snake($name).'.php';
    }

    protected function resolveMigrationPaths(): array
    {
        return !empty(config('settings.migrations_path'))
            ? [config('settings.migrations_path')]
            : config('settings.migrations_paths');
    }
}
