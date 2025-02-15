<?php

namespace Siteman\Cms\Widgets;

use Filament\Widgets\Widget;
use Spatie\Health\Enums\Status;

class HealthCheckResultWidget extends Widget
{
    protected static bool $isLazy = false;

    public array $result = [];

    protected static string $view = 'siteman::widgets.health-check-result-widget';

    public function getIcon(): string
    {
        return 'heroicon-o-'.match ($this->result['status'] ?? null) {
            Status::ok()->value => 'check-circle',
            Status::warning()->value => 'exclamation-circle',
            Status::skipped()->value => 'arrow-circle-right',
            Status::failed()->value => 'x-circle',
            default => 'x-circle',
        };
    }

    public function getColor()
    {
        return match ($this->result['status'] ?? null) {
            Status::ok()->value => 'text-success',
            Status::warning()->value => 'text-warning-600',
            Status::skipped()->value => 'text-info-600',
            Status::failed()->value => 'text-danger-600',
            default => 'text-gray-600',
        };
    }
}
