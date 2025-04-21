<?php declare(strict_types=1);

namespace Siteman\Cms\Blocks;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Contracts\View\View;
use Siteman\Cms\Models\Page;

class ImageBlock extends BaseBlock
{
    public function id(): string
    {
        return 'image-block';
    }

    protected function fields(): array
    {
        return [
            SpatieMediaLibraryFileUpload::make('image')
                // uses the hidden image field path OR the current path
                ->collection(function (FileUpload $component, Get $get) {
                    return $get('image_collection_id') ?? $component->getContainer()->getStatePath();
                })
                ->afterStateHydrated(null)
                ->mutateDehydratedStateUsing(null)
                ->responsiveImages()
                ->imageEditor()
                // sets the hidden image field to the state path OR the previous path
                ->afterStateUpdated(function (FileUpload $component, Set $set) {
                    $set('image_collection_id', $component->getContainer()->getStatePath());
                })
                ->live(),
            // we can now call $yourModel->getMedia($value_in_image_collection_id)->first()
            Hidden::make('image_collection_id'),
        ];
    }

    public function render(array $data, Page $page): View
    {
        return \view($this->getView($data, 'siteman::blocks.image-block'), ['image' => $page->getMedia($data['image_collection_id'])->first()]);
    }
}
