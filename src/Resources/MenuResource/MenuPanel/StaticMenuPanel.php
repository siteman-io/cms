<?php

declare(strict_types=1);

namespace Siteman\Cms\Resources\MenuResource\MenuPanel;

use Closure;

class StaticMenuPanel extends AbstractMenuPanel
{
    protected array $items = [];

    public static function make(string $name = 'Static Menu'): self
    {
        return new self($name);
    }

    public function add(string $title, Closure|string $url): static
    {
        $this->items[] = [
            'title' => $title,
            'url' => $url,
        ];

        return $this;
    }

    public function addMany(array $items): static
    {
        foreach ($items as $title => $url) {
            $this->add($title, $url);
        }

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
