<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\Menus\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Siteman\Cms\Resources\Menus\HasLocationAction;
use Siteman\Cms\Resources\Menus\Livewire\CreateCustomLink;
use Siteman\Cms\Resources\Menus\Livewire\CreateCustomText;
use Siteman\Cms\Resources\Menus\Livewire\CreatePageLink;
use Siteman\Cms\Resources\Menus\Livewire\MenuItems;
use Siteman\Cms\Resources\Menus\MenuResource;

class EditMenu extends EditRecord
{
    use HasLocationAction;

    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            $this->getLocationAction(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('siteman::menu.resource.edit.title');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            ...parent::content($schema)->getComponents(),

            Grid::make()
                ->columns(3)
                ->schema([
                    Section::make()
                        ->columns(1)
                        ->schema([
                            Livewire::make(CreatePageLink::class, ['menu' => $this->getRecord()]),
                            Livewire::make(CreateCustomLink::class, ['menu' => $this->getRecord()]),
                            Livewire::make(CreateCustomText::class, ['menu' => $this->getRecord()]),
                        ]),
                    Section::make()
                        ->columnSpan(2)
                        ->schema([
                            Livewire::make(MenuItems::class, ['menu' => $this->getRecord()]),
                        ]),
                ]),
        ]);
    }
}
