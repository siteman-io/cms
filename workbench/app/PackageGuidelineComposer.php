<?php declare(strict_types=1);

namespace Workbench\App;

use Laravel\Boost\Install\GuidelineComposer;
use Laravel\Boost\Install\GuidelineConfig;
use Laravel\Boost\Install\Herd;
use Laravel\Roster\Roster;

class PackageGuidelineComposer extends GuidelineComposer
{
    protected string $userGuidelineDir = __DIR__.'/../../.ai/guidelines';

    public function __construct(protected Roster $roster, protected Herd $herd)
    {
        parent::__construct($roster, $herd);
        $this->config->enforceTests = true;
        $this->config->laravelStyle = true;
        $this->config->caresAboutLocalization = true;
    }

    public function config(GuidelineConfig $config): GuidelineComposer
    {
        return $this;
    }
}
