<?php declare(strict_types=1);

namespace Siteman\Cms\Commands\Generator;

use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\View\View;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use Siteman\Cms\Blocks\BaseBlock;
use Siteman\Cms\Models\Page;

class BlockGenerator
{
    public function generate(string $className, string $namespace, string $blockId): string
    {
        $file = new PhpFile;
        $file->setStrictTypes();

        $ns = $file->addNamespace($namespace);
        $ns->addUse(TextInput::class);

        $class = $ns->addClass($className);
        $class->setExtends(BaseBlock::class);

        $class->addMethod('id')
            ->setPublic()
            ->setReturnType('string')
            ->setBody('return ?;', [$blockId]);

        $class->addMethod('fields')
            ->setProtected()
            ->setReturnType('array')
            ->setBody("return [TextInput::make('title')];");

        $renderMethod = $class->addMethod('render')
            ->setPublic()
            ->setReturnType(View::class)
            ->setBody('return view(?, [\'data\' => $data]);', ["blocks.{$blockId}"]);

        $renderMethod->addParameter('data')->setType('array');
        $renderMethod->addParameter('page')->setType(Page::class);

        return (new PsrPrinter)->printFile($file);
    }

    public function generateView(): string
    {
        return <<<'BLADE'
<div class="block">
    <h2>{{ $data['title'] }}</h2>
</div>
BLADE;
    }
}
