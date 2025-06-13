<?php
declare(strict_types=1);

namespace Siteman\Cms\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Siteman\Cms\Resources\MenuResource\Pages\ListMenus;
use Siteman\Cms\Resources\MenuResource\Pages\EditMenu;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Menu;

class MenuResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'name';

    public static function getModel(): string
    {
        return Menu::class;
    }

    public static function getNavigationLabel(): string
    {
        return __('siteman::menu.resource.navigation-label');
    }

    public static function getLabel(): string
    {
        return self::getNavigationLabel();
    }

    public static function getNavigationIcon(): string
    {
        return __('siteman::menu.resource.navigation-icon');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('siteman::menu.resource.navigation-group');
    }

    public static function getPluralModelLabel(): string
    {
        return __('siteman::menu.resource.navigation-label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(4)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('siteman::menu.resource.fields.name.label'))
                            ->required()
                            ->columnSpan(3),

                        ToggleButtons::make('is_visible')
                            ->grouped()
                            ->options([
                                true => __('siteman::menu.resource.fields.is_visible.visible'),
                                false => __('siteman::menu.resource.fields.is_visible.hidden'),
                            ])
                            ->colors([
                                true => 'primary',
                                false => 'danger',
                            ])
                            ->required()
                            ->label(__('siteman::menu.resource.fields.is_visible.label'))
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $locations = Siteman::getMenuLocations();

        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('menuItems'))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('siteman::menu.resource.fields.name.label')),
                TextColumn::make('locations.location')
                    ->label(__('siteman::menu.resource.fields.locations.label'))
                    ->default(__('siteman::menu.resource.fields.locations.empty'))
                    ->color(fn (string $state) => array_key_exists($state, $locations) ? 'primary' : 'gray')
                    ->formatStateUsing(fn (string $state) => $locations[$state] ?? $state)
                    ->limitList(2)
                    ->sortable()
                    ->badge(),
                TextColumn::make('menu_items_count')
                    ->label(__('siteman::menu.resource.fields.items.label'))
                    ->icon('heroicon-o-link')
                    ->numeric()
                    ->default(0)
                    ->sortable(),
                IconColumn::make('is_visible')
                    ->label(__('siteman::menu.resource.fields.is_visible.label'))
                    ->sortable()
                    ->alignCenter()
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->alignRight()
                    ->dateTimeTooltip()
                    ->since(),
            ])
            ->recordActions(ActionGroup::make([
                EditAction::make(),
                DeleteAction::make()->color('gray'),
            ]))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}
