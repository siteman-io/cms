<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\Pages\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Siteman\Cms\Models\Page;
use Siteman\Cms\Resources\Pages\PageResource;

class PagesTable
{
    public static function configure(Table $table)
    {
        $table = $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('author')->latest('created_at'))
            ->columns([
                TextColumn::make('title')
                    ->label(__('siteman::page.table.columns.title'))
                    ->searchable()
                    ->formatStateUsing(fn (Page $record) => $record->slug === '/' ? new HtmlString(Blade::render("<div class='flex'><span>$record->title &nbsp&nbsp-&nbsp&nbsp</span><x-filament::badge class='inline-block'>Homepage</x-filament::badge></div>")) : $record->title)
                    ->sortable(),
                TextColumn::make('author.name')
                    ->label(__('siteman::page.table.columns.author'))
                    ->searchable()
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label(__('siteman::page.table.columns.published_at'))
                    ->since()
                    ->dateTimeTooltip()
                    ->alignRight()
                    ->width('10rem')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('author')
                    ->label(__('siteman::page.table.filters.author.label'))
                    ->multiple()
                    ->relationship('author', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label(__('siteman::page.table.actions.edit'))
                        ->url(fn (Page $record): string => PageResource::getUrl('index', ['selectedPageId' => $record->id])),
                    DeleteAction::make()->label(__('siteman::page.table.actions.delete'))
                        ->color('gray')
                        ->successNotification(fn (Notification $notification) => $notification->title(__('siteman::page.notifications.deleted.title'))),
                    ForceDeleteAction::make()->color('gray'),
                    RestoreAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label(__('siteman::page.table.actions.delete')),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);

        return $table;
    }
}
