<?php declare(strict_types=1);

namespace Siteman\Cms\Search;

use Filament\GlobalSearch\Providers\DefaultGlobalSearchProvider;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Pages\SettingsPage;

class SitemanSearchProvider extends DefaultGlobalSearchProvider
{
    public function getResults(string $query): ?GlobalSearchResults
    {
        $builder = parent::getResults($query);
        if (!SettingsPage::canAccess()) {
            return $builder;
        }
        foreach (Siteman::registeredSettingsForms() as $settingsForm) {
            $group = $settingsForm::getSettingsClass()::group();
            if (str_contains(strtolower($group), strtolower($query))) {
                $builder->category('Settings', [new GlobalSearchResult(
                    str($group)->headline()->toString(),
                    SettingsPage::getUrl(['group' => $group]),
                )]);
            }
        }

        return $builder;
    }
}
