<?php

use Illuminate\Support\Facades\File;
use Siteman\Cms\Commands\MakeBlockCommand;

use function Pest\Laravel\artisan;

it('creates a new block', function () {
    File::shouldReceive('exists')
        ->andReturn(false);

    File::shouldReceive('get')
        ->andReturn('stub content');

    File::shouldReceive('ensureDirectoryExists')
        ->once();

    File::shouldReceive('put')
        ->once()
        ->withArgs(function ($path, $content) {
            return str_contains($path, 'App/Blocks/MyBlock.php') && str_contains($content, 'stub content');
        });

    File::shouldReceive('makeDirectory')
        ->once()
        ->withArgs(function ($path, $mode, $recursive) {
            return str_contains($path, 'resources/views/blocks') && $mode === 0755 && $recursive === true;
        });

    File::shouldReceive('put')
        ->once()
        ->withArgs(function ($path, $content) {
            return str_contains($path, 'resources/views/blocks/my.blade.php') && str_contains($content, '<div class="block">');
        });

    artisan(MakeBlockCommand::class, ['name' => 'MyBlock'])
        ->expectsOutputToContain('Block class created successfully.')
        ->expectsOutputToContain('View file created at: resources/views/blocks/my.blade.php')
        ->expectsOutputToContain('Remember to register your block in the configure method of your theme.')
        ->assertExitCode(0);
});
