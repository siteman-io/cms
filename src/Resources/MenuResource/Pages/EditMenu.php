<?php
declare(strict_types=1);

namespace Siteman\Cms\Resources\MenuResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Siteman\Cms\Resources\MenuResource;
use Siteman\Cms\Resources\MenuResource\HasLocationAction;

class EditMenu extends EditRecord
{
    use HasLocationAction;

    protected static string $view = 'siteman::resources.menu.pages.edit-record';

    public static function getResource(): string
    {
        return MenuResource::class;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema($form->getComponents()),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            $this->getLocationAction(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('siteman::menu.resource.edit.title');
    }
}
