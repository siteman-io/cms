<?php

declare(strict_types=1);

namespace Siteman\Cms\Commands\Generator;

use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;

class BlockGenerator
{
    public function generate(string $className, string $namespace, string $blockId): string
    {
        $file = new PhpFile;
        $file->setStrictTypes();

        $ns = $file->addNamespace($namespace);
        $ns->addUse('Filament\Forms\Components\TextInput');
        $ns->addUse('Illuminate\Contracts\View\View');
        $ns->addUse('Siteman\Cms\Blocks\BaseBlock');
        $ns->addUse('Siteman\Cms\Models\Page');

        $class = $ns->addClass($className);
        $class->setExtends('Siteman\Cms\Blocks\BaseBlock');

        $class->addMethod('id')
            ->setPublic()
            ->setReturnType('string')
            ->setBody('return ?;', [$blockId]);

        $class->addMethod('fields')
            ->setProtected()
            ->setReturnType('array')
            ->setBody("return [\n    TextInput::make('title'),\n];");

        $renderMethod = $class->addMethod('render')
            ->setPublic()
            ->setReturnType('Illuminate\Contracts\View\View')
            ->setBody('return view(?, [\'data\' => $data]);', ["blocks.{$blockId}"]);

        $renderMethod->addParameter('data')->setType('array');
        $renderMethod->addParameter('page')->setType('Siteman\Cms\Models\Page');

        return (new PsrPrinter)->printFile($file);
    }

    public function generateView(string $blockId): string
    {
        return <<<'BLADE'
<div class="block">
    <h2>{{ $data['title'] }}</h2>
</div>
BLADE;
    }
}
