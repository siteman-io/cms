<?php declare(strict_types=1);

namespace Siteman\Cms\Settings;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;

class BlogSettingsForm implements SettingsFormInterface
{
    public static function getSettingsClass(): string
    {
        return BlogSettings::class;
    }

    public function icon(): string
    {
        return 'heroicon-o-newspaper';
    }

    public function schema(): array
    {
        return [
            Checkbox::make('enabled')
                ->label(__('siteman::settings.blog.fields.enabled.label'))
                ->helperText(__('siteman::settings.blog.fields.enabled.helper-text'))
                ->live(),
            Group::make()->schema([
                TextInput::make('blog_index_route')
                    ->prefix('/')
                    ->label(__('siteman::settings.blog.fields.blog_index_route.label'))
                    ->helperText(__('siteman::settings.blog.fields.blog_index_route.helper-text')),
                TextInput::make('tag_index_route')
                    ->prefix('/')
                    ->label(__('siteman::settings.blog.fields.tag_index_route.label'))
                    ->helperText(__('siteman::settings.blog.fields.tag_index_route.helper-text')),
                Fieldset::make(__('siteman::settings.blog.fields.fieldsets.rss'))->schema([
                    Checkbox::make('rss_enabled')
                        ->columns(1)
                        ->label(__('siteman::settings.blog.fields.rss_enabled.label'))
                        ->helperText(__('siteman::settings.blog.fields.rss_enabled.helper-text'))
                        ->live(),
                    TextInput::make('rss_endpoint')
                        ->columns(1)
                        ->label(__('siteman::settings.blog.fields.rss_endpoint.label'))
                        ->helperText(__('siteman::settings.blog.fields.rss_endpoint.helper-text'))
                        ->visible(fn (Get $get) => ($get('rss_enabled') ?? false) === true),
                ]),
            ])->visible(fn (Get $get) => ($get('enabled') ?? false) === true),
        ];
    }
}
