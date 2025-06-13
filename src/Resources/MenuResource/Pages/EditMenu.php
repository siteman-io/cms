<?php
declare(strict_types=1);

namespace Siteman\Cms\Resources\MenuResource\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\DeleteAction;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Siteman\Cms\Resources\MenuResource;
use Siteman\Cms\Resources\MenuResource\HasLocationAction;

class EditMenu extends EditRecord
{
    use HasLocationAction;

    protected string $view = 'siteman::resources.menu.pages.edit-record';

    public static function getResource(): string
    {
        return MenuResource::class;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema($schema->getComponents()),
            ]);
    }

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
}
