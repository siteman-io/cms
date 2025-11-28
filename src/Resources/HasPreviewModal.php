<?php declare(strict_types=1);

namespace Siteman\Cms\Resources;

trait HasPreviewModal
{
    protected function getPreviewModalView(): ?string
    {
        return 'siteman::preview';
    }

    protected function getPreviewModalDataRecordKey(): ?string
    {
        return 'page';
    }
}
