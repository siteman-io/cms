<?php declare(strict_types=1);

namespace Siteman\Cms\Resources;

use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Siteman\Cms\Blocks\BlockBuilder;
use Siteman\Cms\Concerns\HasFormHooks;
use Siteman\Cms\Enums\FormHook;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\BasePostType;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\PageResource\Pages;
use Siteman\Cms\Resources\PageResource\Widgets\HomePageWidget;

class PageResource extends Resource
{
    use HasFormHooks;

    protected static ?string $model = Page::class;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpan('full')
                    ->tabs(self::hook([self::getContentTab()], FormHook::POST_TABS)),
            ]);
    }

    public static function getContentTab(): Tab
    {
        return Tab::make('Content')
            ->columns(4)
            ->schema([
                Forms\Components\Section::make()
                    ->columnSpan(3)
                    ->schema(self::hook([
                        TextInput::make('title')
                            ->label(__('siteman::page.fields.title.label'))
                            ->helperText(__('siteman::page.fields.title.helper-text'))
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $state) {
                                if (!$get('is_slug_changed_manually') && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->live(debounce: 300)
                            ->required(),
                        BlockBuilder::make('blocks'),
                    ], FormHook::POST_MAIN)),
                Forms\Components\Section::make()
                    ->columnSpan(1)
                    ->schema(self::hook([
                        Forms\Components\Select::make('type')->options([
                            'page' => 'Page',
                            'blog_index' => 'Blog Index',
                        ])->required(),
                        TextInput::make('slug')
                            ->label('siteman::page.fields.slug.label')
                            ->translateLabel()
                            ->helperText(__('siteman::page.fields.slug.helper-text'))
                            ->afterStateUpdated(function (Forms\Set $set) {
                                $set('is_slug_changed_manually', true);
                            })
                            ->required(),
                        Hidden::make('is_slug_changed_manually')
                            ->default(false)
                            ->dehydrated(false),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('siteman::page.fields.published_at.label')
                            ->translateLabel()
                            ->helperText(__('siteman::page.fields.published_at.helper-text'))
                            ->seconds(false),
                        Forms\Components\Select::make('layout')
                            ->label(__('siteman::page.fields.layout.label'))
                            ->helperText(__('siteman::page.fields.layout.helper-text'))
                            ->options(array_keys(Siteman::getLayouts())),
                        Group::make([
                            Textarea::make('description')
                                ->label('siteman::page.fields.description.label')
                                ->translateLabel()
                                ->helperText(__('siteman::page.fields.description.helper-text'))
                                ->columnSpan(2),
                            // here we can add further SEO fields
                        ])
                            ->afterStateHydrated(function (Group $component, ?Page $record): void {
                                $component->getChildComponentContainer()->fill(
                                    $record?->seo?->only('description') ?: []
                                );
                            })
                            ->statePath('seo')
                            ->dehydrated(false)
                            ->saveRelationshipsUsing(function (Page $record, array $state): void {
                                $state = collect($state)->only(['description'])->map(fn ($value) => $value ?: null)->all();

                                if ($record->seo->exists) {
                                    $record->seo->update($state);
                                } else {
                                    $record->seo()->create($state);
                                }
                            }),
                    ], FormHook::POST_SIDEBAR)),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('title'),
                Infolists\Components\TextEntry::make('computed_slug'),
                Infolists\Components\TextEntry::make('published_at')->dateTime()->since()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('author')->latest('created_at'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('siteman::page.table.columns.title'))
                    ->searchable()
                    ->formatStateUsing(fn (BasePostType $record) => $record->slug === '/' ? new HtmlString(Blade::render("<div class='flex'><span>$record->title &nbsp&nbsp-&nbsp&nbsp</span><x-filament::badge class='inline-block'>Homepage</x-filament::badge></div>")) : $record->title)
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label(__('siteman::page.table.columns.author'))
                    ->searchable()
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('siteman::page.table.columns.published_at'))
                    ->since()
                    ->dateTimeTooltip()
                    ->alignRight()
                    ->width('10rem')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('author')
                    ->label(__('siteman::page.table.filters.author.label'))
                    ->multiple()
                    ->relationship('author', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->label(__('siteman::page.table.actions.edit')),
                    Tables\Actions\DeleteAction::make()->label(__('siteman::page.table.actions.delete'))
                        ->color('gray')
                        ->successNotification(fn (Notification $notification) => $notification->title(__('siteman::page.notifications.deleted.title'))),
                    Tables\Actions\ForceDeleteAction::make()->color('gray'),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('siteman::page.table.actions.delete')),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return __('siteman::page.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            HomePageWidget::class,
        ];
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
