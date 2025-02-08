<?php declare(strict_types=1);

namespace Siteman\Cms\Blocks;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Siteman\Cms\Facades\Siteman;

class BlockBuilder extends Builder
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->label(__('siteman::resources/post.fields.blocks.label'));
        $this->helperText(__('siteman::resources/post.fields.blocks.helper-text'));
        $this->blocks(
            $this->registry()
                ->all()
                ->map(fn (BlockInterface $block) => $block->getBlock())
                ->all(),
        );

        $this->blockNumbers(false);
        $this->extraItemActions(actions: [
            Action::make('Disable')
                ->icon(function (array $arguments, Builder $component) {
                    $fullState = $component->getState();
                    if (($fullState[$arguments['item']]['disabled'] ?? false) === true) {
                        return 'heroicon-o-eye';
                    }

                    return 'heroicon-o-eye-slash';
                })
                ->action(function (array $arguments, Builder $component, Action $action, ?Model $record = null): void {
                    $fullState = $component->getState();

                    $isDisabled = ($fullState[$arguments['item']]['disabled'] ?? false) === true;

                    $fullState[$arguments['item']]['disabled'] = !$isDisabled;

                    $component->state($fullState);

                    if ($record) {
                        $record->update(['blocks' => array_values($fullState)]);
                    }

                    Notification::make('foo')->title('updated')->send();
                }),
        ]);
    }

    protected function registry(): BlockRegistry
    {
        return Siteman::blocks();
    }
}
