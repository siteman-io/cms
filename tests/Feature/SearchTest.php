<?php

use Filament\GlobalSearch\GlobalSearchResults;
use Filament\Livewire\GlobalSearch;

use function Pest\Livewire\livewire;

it('does not find settings in search if not authorized', function () {
    $this->actingAs(createUser());

    livewire(GlobalSearch::class, ['search' => 'general'])
        ->call('getResults')
        ->assertViewHas('results', fn (GlobalSearchResults $results) => $results->getCategories()->count() === 0);
});

it('finds settings in search if authorized', function () {
    $this->actingAs(createUser(permissions: ['page_SettingsPage']));

    livewire(GlobalSearch::class, ['search' => 'general'])
        ->call('getResults')
        ->assertViewHas(
            'results',
            fn (GlobalSearchResults $results) => $results->getCategories()->count() === 1
                && $results->getCategories()->first()[0]->title === 'General',
        );
});
