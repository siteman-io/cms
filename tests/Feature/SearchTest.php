<?php

use Filament\GlobalSearch\GlobalSearchResults;
use Filament\Livewire\GlobalSearch;
use Workbench\App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('does not find settings in search if not authorized', function () {
    actingAs(User::factory()->create());

    livewire(GlobalSearch::class, ['search' => 'general'])
        ->call('getResults')
        ->assertViewHas('results', fn (GlobalSearchResults $results) => $results->getCategories()->count() === 0);
});

it('finds settings in search if authorized', function () {
    actingAs(User::factory()->withPermissions(['page_SettingsPage'])->create());

    livewire(GlobalSearch::class, ['search' => 'general'])
        ->call('getResults')
        ->assertViewHas(
            'results',
            fn (GlobalSearchResults $results) => $results->getCategories()->count() === 1
                && $results->getCategories()->first()[0]->title === 'General',
        );
});
