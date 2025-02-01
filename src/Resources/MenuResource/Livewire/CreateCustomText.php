<?php
declare(strict_types=1);

namespace Siteman\Cms\Resources\MenuResource\Livewire;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Siteman\Cms\Models\Menu;

class CreateCustomText extends Component implements HasForms
{
    use InteractsWithForms;

    public Menu $menu;

    public string $title = '';

    public function save(): void
    {
        $this->validate([
            'title' => ['required', 'string'],
        ]);

        $this->menu
            ->menuItems()
            ->create([
                'title' => $this->title,
                'order' => $this->menu->menuItems->max('order') + 1,
            ]);

        Notification::make()
            ->title(__('siteman::menu.notifications.created.title'))
            ->success()
            ->send();

        $this->reset('title');
        $this->dispatch('menu:created');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label(__('siteman::menu.form.title'))
                    ->required(),
            ]);
    }

    public function render(): View
    {
        return view('siteman::menu.livewire.create-custom-text');
    }
}
