<?php declare(strict_types=1);

namespace Siteman\Cms\Resources;

use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Form;
use Siteman\Cms\Enums\FormHook;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Post;
use Siteman\Cms\Resources\PostResource\Pages;
use Siteman\Cms\Settings\BlogSettings;

class PostResource extends BasePostResource
{
    protected static ?string $model = Post::class;

    public static function form(Form $form): Form
    {
        Siteman::registerFormHook(FormHook::POST_MAIN, function ($fields) {
            return [array_shift($fields), Forms\Components\Textarea::make('excerpt')
                ->label(__('siteman::post.fields.excerpt.label'))
                ->helperText(__('siteman::post.fields.excerpt.helper-text'))
                ->rows(3), ...$fields];
        });
        Siteman::registerFormHook(FormHook::POST_SIDEBAR, function ($fields) {
            // We remove the last field, which is the layout field
            array_pop($fields);

            return array_merge($fields, [
                SpatieMediaLibraryFileUpload::make('Image')
                    ->label('siteman::post.fields.image.label')
                    ->translateLabel()
                    ->helperText(__('siteman::post.fields.image.helper-text'))
                    ->collection('featured_image')
                    ->imageEditor(),
                SpatieTagsInput::make('tags')
                    ->label('siteman::post.fields.tags.label')
                    ->translateLabel()
                    ->helperText(__('siteman::post.fields.tags.helper-text')),
            ]);
        });

        return parent::form($form);
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

    public static function shouldRegisterNavigation(): bool
    {
        return app(BlogSettings::class)->enabled;
    }
}
