<?php

namespace Siteman\Cms\Resources;

use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Siteman\Cms\Blocks\BlockBuilder;
use Siteman\Cms\Concerns\HasFormHooks;
use Siteman\Cms\Enums\FormHook;
use Siteman\Cms\Facades\Siteman;

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
                            ->label(__('siteman::resources/page.fields.layout.label'))
                            ->helperText(__('siteman::resources/page.fields.layout.helper-text'))
                            ->options(array_keys(Siteman::getLayouts())),
                    ], FormHook::POST_SIDEBAR)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('siteman::resources/post.table.columns.id'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('siteman::resources/post.table.columns.title'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label(__('siteman::resources/post.table.columns.author'))
                    ->searchable()
                    ->alignRight()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('siteman::resources/post.table.columns.published_at'))
                    ->since()
                    ->alignRight()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('published')
                    ->label(__('siteman::resources/post.table.filters.published.label'))
                    ->query(fn (Builder $query) => $query->scopes(['published'])),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('siteman::resources/post.table.actions.edit')
                    ->translateLabel(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('siteman::resources/post.table.bulk-actions.delete')
                        ->translateLabel(),
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
        return self::getNavigationLabel();
    }
}
