<?php

namespace Siteman\Cms\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\text;

#[AsCommand(name: 'make:siteman-block')]
class MakeBlockCommand extends Command
{
    use CanManipulateFiles;

    public $signature = 'make:siteman-block {name?}';

    public $description = 'Create siteman block';

    public function handle(): int
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
        $blockNamespace = str($block)->contains('\\') ?
            (string) str($block)->beforeLast('\\') :
            app()->getNamespace().'Blocks';

        $blockId = str($blockClass)->beforeLast('Block')->kebab();

        // Create the block class
        $this->copyStubToApp('Block', base_path(str($blockNamespace)->replace('\\', '/').'/'.$blockClass.'.php'), [
            'class' => $blockClass,
            'namespace' => $blockNamespace,
            'id' => $blockId,
        ]);
        $this->components->info('Block class created successfully.');

        // Create the view file
        $viewsPath = resource_path('views/blocks');
        if (!File::exists($viewsPath)) {
            File::makeDirectory($viewsPath, 0755, true);
        }

        $viewContent = <<<'BLADE'
<div class="block">
    <h2>{{ $data['title'] }}</h2>
</div>
BLADE;

        File::put("{$viewsPath}/{$blockId}.blade.php", $viewContent);
        $this->components->info("View file created at: resources/views/blocks/{$blockId}.blade.php");

        $this->components->info('Remember to register your block in the configure method of your theme.');

        return self::SUCCESS;
    }
}
