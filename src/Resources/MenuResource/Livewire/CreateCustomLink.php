<?php

declare(strict_types=1);

namespace Siteman\Cms\Resources\MenuResource\Livewire;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Resources\MenuResource\LinkTarget;

class CreateCustomLink extends Component implements HasForms
{
    use InteractsWithForms;

    public Menu $menu;

    public string $title = '';

    public string $url = '';

    public string $target = LinkTarget::Self->value;

    public function save(): void
    {
        $this->validate([
            'title' => ['required', 'string'],
            'url' => ['required', 'string'],
            'target' => ['required', 'string', Rule::in(LinkTarget::cases())],
        ]);

        $this->menu
            ->menuItems()
            ->create([
                'title' => $this->title,
                'url' => $this->url,
                'target' => $this->target,
                'order' => $this->menu->menuItems->max('order') + 1,
            ]);

        Notification::make()
            ->title(__('siteman::menu.notifications.created.title'))
            ->success()
            ->send();

        $this->reset('title', 'url', 'target');
        $this->dispatch('menu:created');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('siteman::menu.form.title'))
                    ->required(),
                TextInput::make('url')
                    ->label(__('siteman::menu.form.url'))
                    ->required(),
                Select::make('target')
                    ->label(__('siteman::menu.open_in.label'))
                    ->options(LinkTarget::class)
                    ->default(LinkTarget::Self),
            ]);
    }

    public function render(): View
    {
        return view('siteman::resources.menu.livewire.create-custom-link');
    }
}
