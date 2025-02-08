<?php

namespace Siteman\Cms\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
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
            app()->getNamespace().'Themes';

        $relativeViewPath = 'themes/'.str($themeClass)->replace('Theme', '')->kebab();
        $themeViewsPath = resource_path('views/'.$relativeViewPath);
        $folderToCopy = dirname(__DIR__, 2).'/resources/views/themes/blank';

        $this->copyStubToApp('Theme', base_path(str($themeNamespace)->replace('\\', '/').'/'.$themeClass.'.php'), [
            'class' => $themeClass,
            'namespace' => $themeNamespace,
            'themeResourcePath' => str($relativeViewPath)->replace('/', '.'),
        ]);
        $this->components->info('Theme class created.');

        File::copyDirectory($folderToCopy, $themeViewsPath);
        $this->components->info('Theme views created.');

        $this->components->info('You may change the theme via your config/siteman.php');

        return self::SUCCESS;
    }
}
