<?php declare(strict_types=1);

namespace Siteman\Cms\Commands\Generator;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Support\Facades\File;

class ThemeGenerator
{
    use CanManipulateFiles;

    public function generate(string $themeClass, string $themeNamespace): void
    {
        $relativeViewPath = 'themes/'.str($themeClass)->lower()->replace('theme', '')->kebab();
        $folderToCopy = dirname(__DIR__, 3).'/resources/views/themes/blank';

        $this->copyStubToApp('Theme', base_path(str($themeNamespace)->replace('\\', '/').'/'.$themeClass.'.php'), [
            'class' => $themeClass,
            'namespace' => $themeNamespace,
        ]);

        $themeViewsPath = resource_path('views/'.$relativeViewPath);
        File::copyDirectory($folderToCopy, $themeViewsPath);
        foreach (File::allFiles($themeViewsPath) as $viewFile) {
            $content = str($viewFile->getContents())
                ->replace('siteman::themes.blank', str($relativeViewPath)->replace('/', '.'));
            File::put($viewFile->getPathname(), $content->toString());
        }
    }
}
