<?php

namespace Siteman\Cms\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Siteman\Cms\Commands\Generator\ThemeGenerator;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:siteman-theme')]
class MakeThemeCommand extends Command
{
    use CanManipulateFiles;

    public $signature = 'make:siteman-theme {name?}';

    public $description = 'Create new Siteman Theme';

    public function handle(ThemeGenerator $generator): int
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
        $themeClass = $this->prepareClassName($theme, 'Theme');
        $themeNamespace = str($theme)->contains('\\') ?
            (string) str($theme)->beforeLast('\\') :
            app()->getNamespace().'Themes';

        $generator->generate($themeClass, $themeNamespace);

        $this->components->info('Theme class created.');
        $this->components->info('Theme views created.');
        $this->components->info('You may add the theme in your config/siteman.php');

        return self::SUCCESS;
    }

    private function prepareClassName(string $name, string $suffix): string
    {
        return (string) str($name)
            ->afterLast('\\')
            ->studly()
            ->replace($suffix, '')
            ->append($suffix);
    }
}
