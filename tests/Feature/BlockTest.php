<?php

use Siteman\Cms\Resources\Pages\Pages\CreatePage;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('can hide a block', function () {
    actingAs(User::factory()->withPermissions(['view_any_page', 'create_page'])->create());

    $component = livewire(CreatePage::class)
        ->fillForm([
            'blocks' => [
                [
                    'type' => 'markdown-block',
                    'data' => [
                        'content' => '',
                    ],
                ],
            ],
        ]);

    // No clue how to test this yet:
    //  1. Get the blocks field
    //  2. Get the first nested block
    //  3. call the action disable on it
    //  4. assert that the state is changed as expected
})->todo();
