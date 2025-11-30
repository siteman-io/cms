<?php

declare(strict_types=1);

namespace Siteman\Cms\Commands\Generator;

use Illuminate\Support\Facades\File;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;

class ThemeGenerator
{
    public function generate(string $themeClass, string $themeNamespace): string
    {
        $file = new PhpFile;
        $file->setStrictTypes();

        $ns = $file->addNamespace($themeNamespace);
        $ns->addUse('Siteman\Cms\Siteman');
        $ns->addUse('Siteman\Cms\Theme\ThemeInterface');
        $ns->addUse('Siteman\Cms\Theme\BaseLayout');

        $class = $ns->addClass($themeClass);
        $class->addImplement('Siteman\Cms\Theme\ThemeInterface');

        $class->addMethod('getName')
            ->setPublic()
            ->setStatic()
            ->setReturnType('string')
            ->setBody('return ?;', [$themeClass]);

        $configureMethod = $class->addMethod('configure')
            ->setPublic()
            ->setReturnType('void');
        $configureMethod->addParameter('siteman')->setType('Siteman\Cms\Siteman');
        $configureMethod->setBody(
            "\$siteman\n".
            "    ->registerMenuLocation('header', 'Header')\n".
            "    ->registerMenuLocation('footer', 'Footer');\n\n".
            '$siteman->registerLayout(BaseLayout::class);'
        );

        return (new PsrPrinter)->printFile($file);
    }

    public function copyThemeViews(string $themeClass): void
    {
        $relativeViewPath = 'themes/'.str($themeClass)->lower()->replace('theme', '')->kebab();
        $folderToCopy = dirname(__DIR__, 3).'/resources/views/themes/blank';

        $themeViewsPath = resource_path('views/'.$relativeViewPath);
        File::copyDirectory($folderToCopy, $themeViewsPath);

        foreach (File::allFiles($themeViewsPath) as $viewFile) {
            $content = str($viewFile->getContents())
                ->replace('siteman::themes.blank', str($relativeViewPath)->replace('/', '.'));
            File::put($viewFile->getPathname(), $content->toString());
        }
    }

    public function getClassFilePath(string $themeNamespace, string $themeClass): string
    {
        return base_path(str($themeNamespace)->replace('\\', '/').'/'.$themeClass.'.php');
    }
}
