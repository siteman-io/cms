<?php

declare(strict_types=1);

namespace Siteman\Cms\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Siteman\Cms\Commands\Generator\BlockGenerator;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:siteman-block')]
class MakeBlockCommand extends Command
{
    public $signature = 'make:siteman-block {name?}';

    public $description = 'Create siteman block';

    public function handle(BlockGenerator $generator): int
    {
        $block = (string) str(
            $this->argument('name') ??
            text(
                label: 'What is the block name?',
                placeholder: 'MyBlock',
                required: true,
            ),
        )
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $blockClass = (string) str($block)->afterLast('\\');
        $blockNamespace = str($block)->contains('\\')
            ? (string) str($block)->beforeLast('\\')
            : app()->getNamespace().'Blocks';

        $blockId = (string) str($blockClass)->beforeLast('Block')->kebab();

        $classPath = $this->getClassPath($blockNamespace, $blockClass);
        File::ensureDirectoryExists(dirname($classPath));
        File::put($classPath, $generator->generate($blockClass, $blockNamespace, $blockId));

        $this->components->info('Block class created successfully.');

        $viewsPath = resource_path('views/blocks');
        File::ensureDirectoryExists($viewsPath);
        File::put("{$viewsPath}/{$blockId}.blade.php", $generator->generateView());

        $this->components->info("View file created at: resources/views/blocks/{$blockId}.blade.php");
        $this->components->info('Remember to register your block in the configure method of your theme.');

        return self::SUCCESS;
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
