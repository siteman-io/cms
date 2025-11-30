<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Siteman\Cms\Commands\MakeBlockCommand;

use function Pest\Laravel\artisan;

beforeEach(function () {
    // Clean up any files created during tests
    $blockPath = app_path('Blocks/TestBlock.php');
    $viewPath = resource_path('views/blocks/test.blade.php');

    if (File::exists($blockPath)) {
        File::delete($blockPath);
    }
    if (File::exists($viewPath)) {
        File::delete($viewPath);
    }
});

afterEach(function () {
    // Clean up files created during tests
    $blockPath = app_path('Blocks/TestBlock.php');
    $viewPath = resource_path('views/blocks/test.blade.php');
    $blocksDir = app_path('Blocks');
    $viewsDir = resource_path('views/blocks');

    if (File::exists($blockPath)) {
        File::delete($blockPath);
    }
    if (File::exists($viewPath)) {
        File::delete($viewPath);
    }
    if (File::isDirectory($blocksDir) && count(File::files($blocksDir)) === 0) {
        File::deleteDirectory($blocksDir);
    }
    if (File::isDirectory($viewsDir) && count(File::files($viewsDir)) === 0) {
        File::deleteDirectory($viewsDir);
    }
});

it('creates a new block', function () {
    artisan(MakeBlockCommand::class, ['name' => 'TestBlock'])
        ->expectsOutputToContain('Block class created successfully.')
        ->expectsOutputToContain('View file created at: resources/views/blocks/test.blade.php')
        ->expectsOutputToContain('Remember to register your block in the configure method of your theme.')
        ->assertExitCode(0);

    $blockPath = app_path('Blocks/TestBlock.php');
    $viewPath = resource_path('views/blocks/test.blade.php');

    expect(File::exists($blockPath))->toBeTrue();
    expect(File::exists($viewPath))->toBeTrue();

    $blockContent = File::get($blockPath);
    expect($blockContent)
        ->toContain('class TestBlock extends BaseBlock')
        ->toContain("return 'test';")
        ->toContain('public function render(array $data, Page $page): View');

    $viewContent = File::get($viewPath);
    expect($viewContent)
        ->toContain('<div class="block">')
        ->toContain("{{ \$data['title'] }}");
});
