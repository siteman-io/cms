<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\Pages\Actions;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Resources\Pages\PageResource;

class CreateAction extends \Filament\Actions\CreateAction
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->model(PageResource::getModel())
            ->schema([
                Group::make()
                    ->schema([
                        TextInput::make('title')
                            ->label(__('siteman::page.fields.title.label'))
                            ->helperText(__('siteman::page.fields.title.helper-text'))
                            ->required()
                            ->live(debounce: 300)
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if (filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label(__('siteman::page.fields.slug.label'))
                            ->helperText(__('siteman::page.fields.slug.helper-text'))
                            ->required(),
                        Select::make('type')
                            ->options(collect(Siteman::getPageTypes())->mapWithKeys(fn ($type, $key) => [$key => str($key)->headline()])->toArray())
                            ->required(),
                        Select::make('parent_id')
                            ->searchable()
                            ->preload()
                            ->relationship('parent', 'title'),

                    ]),
            ])
            ->mutateDataUsing(function (array $data): array {
                // Find the highest order value and increment by 1
                $maxOrder = PageResource::getModel()::max('order') ?? 0;
                $data['order'] = $maxOrder + 1;

                return $data;
            })
            ->after(function ($record) {
                // Redirect to ListPages with the newly created page selected
                return redirect()->to(PageResource::getUrl('index', ['selectedPageId' => $record->id]));
            });
    }
}
