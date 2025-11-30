<?php

declare(strict_types=1);

use Siteman\Cms\Commands\Generator\BlockGenerator;

it('generates a valid block class', function () {
    $generator = new BlockGenerator;

    $output = $generator->generate('TestBlock', 'App\\Blocks', 'test');

    expect($output)
        ->toContain('declare(strict_types=1);')
        ->toContain('namespace App\\Blocks;')
        ->toContain('use Filament\\Forms\\Components\\TextInput;')
        ->toContain('use Illuminate\\Contracts\\View\\View;')
        ->toContain('use Siteman\\Cms\\Blocks\\BaseBlock;')
        ->toContain('use Siteman\\Cms\\Models\\Page;')
        ->toContain('class TestBlock extends BaseBlock')
        ->toContain('public function id(): string')
        ->toContain("return 'test';")
        ->toContain('protected function fields(): array')
        ->toContain("TextInput::make('title')")
        ->toContain('public function render(array $data, Page $page): View')
        ->toContain("return view('blocks.test', ['data' => \$data]);");
});

it('generates block with custom namespace', function () {
    $generator = new BlockGenerator;

    $output = $generator->generate('HeroBlock', 'Acme\\Cms\\Blocks', 'hero');

    expect($output)
        ->toContain('namespace Acme\\Cms\\Blocks;')
        ->toContain('class HeroBlock extends BaseBlock')
        ->toContain("return 'hero';")
        ->toContain("return view('blocks.hero', ['data' => \$data]);");
});

it('generates a view template', function () {
    $generator = new BlockGenerator;

    $output = $generator->generateView('hero');

    expect($output)
        ->toContain('<div class="block">')
        ->toContain("{{ \$data['title'] }}")
        ->toContain('</div>');
});

it('escapes block id correctly in generated code', function () {
    $generator = new BlockGenerator;

    $output = $generator->generate('MySpecialBlock', 'App\\Blocks', 'my-special');

    expect($output)
        ->toContain("return 'my-special';")
        ->toContain("return view('blocks.my-special', ['data' => \$data]);");
});
