<?php declare(strict_types=1);

namespace Siteman\Cms\Resources;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Siteman\Cms\Enums\FormHook;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\PageResource\Pages;
use Siteman\Cms\Resources\PageResource\Widgets\HomePageWidget;

class PageResource extends BasePostResource
{
    protected static ?string $model = Page::class;

    public static function form(Form $form): Form
    {
        Siteman::registerFormHook(FormHook::POST_SIDEBAR, function ($fields) {
            return array_merge($fields, [Group::make([
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
                })]);
        });

        return parent::form($form);
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
