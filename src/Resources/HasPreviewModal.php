<?php declare(strict_types=1);

namespace Siteman\Cms\Resources;

use Pboivin\FilamentPeek\Pages\Concerns\HasPreviewModal as BaseHasPreviewModal;

trait HasPreviewModal
{
    use BaseHasPreviewModal;

    protected function getPreviewModalView(): ?string
    {
        return 'siteman::preview';
    }

    protected function getPreviewModalDataRecordKey(): ?string
    {
        return 'post';
    }
}
