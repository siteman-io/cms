<?php

declare(strict_types=1);

namespace Siteman\Cms\Contracts;

use Illuminate\Support\Collection;

interface MenuItemInterface
{
    public function getTitle(): string;

    public function getUrl(): ?string;

    public function getTarget(): string;

    /**
     * @return Collection<int, MenuItemInterface>
     */
    public function getChildren(): Collection;
}
