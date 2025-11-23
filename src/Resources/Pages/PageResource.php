<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\Pages;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Siteman\Cms\Concerns\HasFormHooks;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\Pages\CreatePage;
use Siteman\Cms\Resources\Pages\Pages\EditPage;
use Siteman\Cms\Resources\Pages\Pages\ListPages;
use Siteman\Cms\Resources\Pages\Pages\PageTreeSplitView;
use Siteman\Cms\Resources\Pages\Tables\PagesTable;

class PageResource extends Resource
{
    use HasFormHooks;

    protected static ?string $model = Page::class;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                Section::make()
                    ->columnSpan(3)
                    ->key('mainPageFields')
                    ->schema(function (Get $get): array {
                        $schema = [
                            TextInput::make('title')
                                ->label(__('siteman::page.fields.title.label'))
                                ->helperText(__('siteman::page.fields.title.helper-text'))
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                    if (!$get('is_slug_changed_manually') && filled($state)) {
                                        $set('slug', Str::slug($state));
                                    }
                                })
                                ->live(debounce: 300)
                                ->required(),
                        ];

                        $type = $get('type');
                        if ($type) {
                            $typeClass = Siteman::getPageTypes()[$type];
                            if (method_exists($typeClass, 'extendPageMainFields')) {
                                $schema = $typeClass::extendPageMainFields($schema);
                            }
                        }

                        return $schema;
                    }),
                Section::make()
                    ->columnSpan(1)
                    ->key('sidebar')
                    ->schema(function (Get $get): array {
                        $schema = [
                            Select::make('type')
                                ->options(collect(Siteman::getPageTypes())->mapWithKeys(fn ($type, $key) => [$key => str($key)->headline()])->toArray())
                                ->afterStateUpdated(function (Select $component, ?Page $record, $state) {
                                    if (!$record) {
                                        return;
                                    }
                                    $all = $record->toArray();
                                    $all['type'] = $state;

                                    $component
                                        ->getContainer()
                                        ->getParentComponent()
                                        ->getContainer()
                                        ->getComponent('mainPageFields')
                                        ?->getChildSchema()
                                        ->fill($all);

                                    $component
                                        ->getContainer()
                                        ->getComponent('sidebar')
                                        ?->getChildSchema()
                                        ->fill($all);
                                }
                                )
                                ->required()
                                ->live(),
                            TextInput::make('slug')
                                ->label('siteman::page.fields.slug.label')
                                ->translateLabel()
                                ->helperText(__('siteman::page.fields.slug.helper-text'))
                                ->afterStateUpdated(function (Set $set) {
                                    $set('is_slug_changed_manually', true);
                                })
                                ->required(),
                            Hidden::make('is_slug_changed_manually')
                                ->default(false)
                                ->dehydrated(false),
                            Select::make('parent_id')
                                ->label(__('siteman::page.fields.parent_id.label'))
                                ->helperText(__('siteman::page.fields.parent_id.helper-text'))
                                ->searchable()
                                ->preload()
                                ->relationship('parent', 'title')
                                ->nullable(),
                            DateTimePicker::make('published_at')
                                ->label('siteman::page.fields.published_at.label')
                                ->translateLabel()
                                ->helperText(__('siteman::page.fields.published_at.helper-text'))
                                ->seconds(false),
                        ];
                        $type = $get('type');
                        if ($type) {
                            $typeClass = Siteman::getPageTypes()[$type];
                            if (method_exists($typeClass, 'extendPageSidebarFields')) {
                                $schema = $typeClass::extendPageSidebarFields($schema);
                            }
                        }

                        return $schema;
                    }),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('computed_slug'),
                TextEntry::make('published_at')->dateTime()->since()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return PagesTable::configure($table);
    }

    public static function getNavigationGroup(): ?string
    {
        return __('siteman::page.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'tree' => PageTreeSplitView::route('/tree'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [];
    }

    public static function getNavigationIcon(): string
    {
        return __('siteman::page.navigation.icon');
    }

    public static function getNavigationLabel(): string
    {
        return __('siteman::page.plural-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('siteman::page.plural-label');
    }

    public static function getLabel(): string
    {
        return __('siteman::page.label');
    }
}
