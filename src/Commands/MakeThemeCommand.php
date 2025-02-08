<?php

namespace Siteman\Cms\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:siteman-theme')]
class MakeThemeCommand extends Command
{
    use CanManipulateFiles;

    public $signature = 'make:siteman-theme {name?}';

    public $description = 'Create siteman theme';

    public function handle(): int
    {
        $theme = (string) str(
            $this->argument('name') ??
            text(
                label: 'What is the theme name?',
                placeholder: 'FancyTheme',
                required: true,
            ),
        )
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $themeClass = (string) str($theme)->afterLast('\\');
        $themeNamespace = str($theme)->contains('\\') ?
            (string) str($theme)->beforeLast('\\') :
            'App\\Themes';

        dump($themeClass, $themeNamespace);
        $this->copyStubToApp('Theme', base_path((string) str($themeNamespace)->replace('\\', DIRECTORY_SEPARATOR)), [
            'class' => $themeClass,
            'namespace' => $themeNamespace,
        ]);

        return self::SUCCESS;
    }
}
