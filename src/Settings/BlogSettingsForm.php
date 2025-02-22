<?php declare(strict_types=1);

namespace Siteman\Cms\Settings;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;

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
                ->label(__('siteman::settings.groups.blog.fields.enabled.label'))
                ->helperText(__('siteman::settings.groups.blog.fields.enabled.helper-text')),
            TextInput::make('blog_index_route')
                ->label(__('siteman::settings.groups.blog.fields.blog_index_route.label'))
                ->helperText(__('siteman::settings.groups.blog.fields.blog_index_route.helper-text')),
            TextInput::make('tag_route_prefix')
                ->label(__('siteman::settings.groups.blog.fields.tag_route_prefix.label'))
                ->helperText(__('siteman::settings.groups.blog.fields.tag_route_prefix.helper-text')),
            Checkbox::make('rss_enabled')
                ->label(__('siteman::settings.groups.blog.fields.rss_enabled.label'))
                ->helperText(__('siteman::settings.groups.blog.fields.rss_enabled.helper-text')),
            TextInput::make('rss_endpoint')
                ->label(__('siteman::settings.groups.blog.fields.rss_endpoint.label'))
                ->helperText(__('siteman::settings.groups.blog.fields.rss_endpoint.helper-text')),
        ];
    }
}
