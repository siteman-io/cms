<?php declare(strict_types=1);

namespace Siteman\Cms\Theme;

use Illuminate\Filesystem\Filesystem;

class ThemeRegistry
{
    public function __construct(
        protected Filesystem $fs,
        protected string $vendorPath,
        protected array $configuredThemes = [],
    ) {}

    public function getThemes(): array
    {
        return array_merge($this->configuredThemes, $this->installedThemes());
    }

    protected function installedThemes(): array
    {
        $packages = [];

        if ($this->fs->exists($path = $this->vendorPath.'/composer/installed.json')) {
            $installed = json_decode($this->fs->get($path), true);

            $packages = $installed['packages'] ?? $installed;
        }
        info($packages);

        return collect($packages)
            ->map(fn ($package) => data_get($package, 'extra.siteman.themes'))
            ->filter()
            ->flatten()
            ->unique()
            ->all();
    }
}
