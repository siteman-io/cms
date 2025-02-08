<?php declare(strict_types=1);

namespace Siteman\Cms\Settings;

interface SettingsFormInterface
{
    public static function getSettingsClass(): string;

    public function icon(): string;

    public function schema(): array;
}
