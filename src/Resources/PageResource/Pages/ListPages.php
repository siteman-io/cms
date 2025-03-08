<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\PageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\View as FacadesView;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Pboivin\FilamentPeek\Support\View;
use Siteman\Cms\Resources\HasPreviewModal;
use Siteman\Cms\Resources\PageResource;

class ListPages extends Page
{
    use HasPreviewModal;
    protected static string $resource = PageResource::class;

    protected static string $view = 'siteman::resources.page.pages.list-pages';

    #[Url]
    public ?int $selectedPageId = null;

    public function mount(): void
    {
        // Handle URL parameter for selectedPageId
        if ($this->selectedPageId) {
            // Validate that the page exists
            $page = PageResource::getModel()::find($this->selectedPageId);
            if (!$page) {
                $this->selectedPageId = null;
            }
        }
    }

    #[On('page-selected')]
    public function onPageSelected(int $pageId): void
    {
        $this->selectedPageId = $pageId;
    }

    protected function getHeaderActions(): array
    {
        FacadesView::share(View::PREVIEW_ACTION_SETUP_HOOK, true);

        return [
            Actions\CreateAction::make()
                ->model(PageResource::getModel())
                ->form([
                    \Filament\Forms\Components\Group::make()
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('title')
                                ->label(__('siteman::page.fields.title.label'))
                                ->helperText(__('siteman::page.fields.title.helper-text'))
                                ->required()
                                ->live(debounce: 300)
                                ->afterStateUpdated(function (\Filament\Forms\Set $set, ?string $state) {
                                    if (filled($state)) {
                                        $set('slug', \Illuminate\Support\Str::slug($state));
                                    }
                                }),
                            \Filament\Forms\Components\TextInput::make('slug')
                                ->label(__('siteman::page.fields.slug.label'))
                                ->helperText(__('siteman::page.fields.slug.helper-text'))
                                ->required(),
                            \Filament\Forms\Components\Select::make('type')
                                ->options(collect(\Siteman\Cms\Facades\Siteman::getPageTypes())->mapWithKeys(fn ($type, $key) => [$key => str($key)->headline()])->toArray())
                                ->required(),
                        ])
                ])
                ->mutateFormDataUsing(function (array $data): array {
                    // Find the highest order value and increment by 1
                    $maxOrder = PageResource::getModel()::max('order') ?? 0;
                    $data['order'] = $maxOrder + 1;
                    
                    return $data;
                })
                ->after(function ($record) {
                    // Redirect to ListPages with the newly created page selected
                    return redirect()->to(PageResource::getUrl('index', ['selectedPageId' => $record->id]));
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PageResource\Widgets\HomePageWidget::class,
        ];
    }
}
