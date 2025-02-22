<?php

namespace Siteman\Cms\Resources;

use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
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

abstract class BasePostResource extends Resource
{
    use HasFormHooks;

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
                            ->label(__('siteman::resources/post.fields.title.label'))
                            ->helperText(__('siteman::resources/post.fields.title.helper-text'))
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
                        TextInput::make('slug')
                            ->label('siteman::resources/post.fields.slug.label')
                            ->translateLabel()
                            ->helperText(__('siteman::resources/post.fields.slug.helper-text'))
                            ->afterStateUpdated(function (Forms\Set $set) {
                                $set('is_slug_changed_manually', true);
                            })
                            ->required(),
                        Hidden::make('is_slug_changed_manually')
                            ->default(false)
                            ->dehydrated(false),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('siteman::resources/post.fields.published_at.label')
                            ->translateLabel()
                            ->helperText(__('siteman::resources/post.fields.published_at.helper-text'))
                            ->seconds(false),
                        Forms\Components\Select::make('layout')
                            ->label(__('siteman::page.fields.layout.label'))
                            ->helperText(__('siteman::page.fields.layout.helper-text'))
                            ->options(array_keys(Siteman::getLayouts())),
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

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('author')->latest('created_at'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('siteman::resources/post.table.columns.title'))
                    ->searchable()
                    ->formatStateUsing(fn (BasePostType $record) => $record->slug === '/' ? new HtmlString(Blade::render("<div class='flex'><span>$record->title &nbsp&nbsp-&nbsp&nbsp</span><x-filament::badge class='inline-block'>Homepage</x-filament::badge></div>")) : $record->title)
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label(__('siteman::resources/post.table.columns.author'))
                    ->searchable()
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('siteman::resources/post.table.columns.published_at'))
                    ->since()
                    ->dateTimeTooltip()
                    ->alignRight()
                    ->width('10rem')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('author')
                    ->label(__('siteman::resources/post.table.filters.author.label'))
                    ->multiple()
                    ->relationship('author', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->label(__('siteman::resources/post.table.actions.edit')),
                    Tables\Actions\DeleteAction::make()->label(__('siteman::resources/post.table.actions.delete'))
                        ->color('gray')
                        ->successNotification(fn (Notification $notification) => $notification->title(__('siteman::resources/post.notifications.deleted.title'))),
                    Tables\Actions\ForceDeleteAction::make()->color('gray'),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('siteman::resources/post.table.actions.delete')),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return __('siteman::resources/post.navigation-group');
    }

    public static function getNavigationIcon(): string
    {
        return __('siteman::resources/post.navigation-icon');
    }

    public static function getNavigationLabel(): string
    {
        return __('siteman::resources/post.navigation-label');
    }

    public static function getLabel(): string
    {
        return __('siteman::resources/post.label');
    }
}
