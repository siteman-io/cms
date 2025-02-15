<?php

namespace Siteman\Cms\Resources;

use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Siteman\Cms\Blocks\BlockBuilder;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Post;
use Siteman\Cms\Resources\PostResource\Pages;
use Siteman\Cms\Settings\BlogSettings;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        self::getContentTab(),
                    ]),

            ]);
    }

    public static function getContentTab(): Tab
    {
        return Tab::make('Content')
            ->columns(4)
            ->schema([
                Forms\Components\Section::make()
                    ->columnSpan(3)
                    ->schema([
                        TextInput::make('title')
                            ->label('siteman::resources/post.fields.title.label')
                            ->translateLabel()
                            ->helperText(__('siteman::resources/post.fields.title.helper-text'))
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $state) {
                                if (!$get('is_slug_changed_manually') && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->live(debounce: 300)
                            ->required(),
                        Forms\Components\Textarea::make('excerpt')
                            ->label('siteman::resources/post.fields.excerpt.label')
                            ->translateLabel()
                            ->helperText(__('siteman::resources/post.fields.excerpt.helper-text'))
                            ->rows(3),
                        BlockBuilder::make('blocks'),
                    ]),
                Forms\Components\Section::make()
                    ->columnSpan(1)
                    ->schema([
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
                        SpatieMediaLibraryFileUpload::make('Image')
                            ->label('siteman::resources/post.fields.image.label')
                            ->translateLabel()
                            ->helperText(__('siteman::resources/post.fields.image.helper-text'))
                            ->collection('featured_image')
                            ->imageEditor(),
                        SpatieTagsInput::make('tags')
                            ->label('siteman::resources/post.fields.tags.label')
                            ->translateLabel()
                            ->helperText(__('siteman::resources/post.fields.tags.helper-text')),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('siteman::resources/post.fields.published_at.label')
                            ->translateLabel()
                            ->helperText(__('siteman::resources/post.fields.published_at.helper-text'))
                            ->seconds(false),
                        Forms\Components\Select::make('layout')
                            ->label(__('siteman::resources/page.fields.layout.label'))
                            ->helperText(__('siteman::resources/page.fields.layout.helper-text'))
                            ->options(array_keys(Siteman::getLayouts())),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('siteman::resources/post.table.columns.id')
                    ->translateLabel()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('siteman::resources/post.table.columns.title')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('siteman::resources/post.table.columns.author')
                    ->translateLabel()
                    ->searchable()
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
            'revisions' => Pages\PostRevisions::route('/{record}/revisions'),
        ];
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

    public static function shouldRegisterNavigation(): bool
    {
        return app(BlogSettings::class)->enabled;
    }
}
