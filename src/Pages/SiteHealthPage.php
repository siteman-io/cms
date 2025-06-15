<?php declare(strict_types=1);

namespace Siteman\Cms\Pages;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Siteman\Cms\Pages\Concerns\IsProtectedPage;
use Siteman\Cms\Widgets\HealthCheckResultWidget;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\ResultStores\ResultStore;

class SiteHealthPage extends Page
{
    use IsProtectedPage;

    protected string $view = 'siteman::pages.site-health';

    protected $listeners = ['refresh-component' => '$refresh'];

    protected function getActions(): array
    {
        return [
            Action::make(__('siteman::site-health.buttons.refresh'))
                ->button()
                ->action('refresh'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return parent::getNavigationGroup() ?? __('siteman::site-health.navigation.group');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return parent::getNavigationIcon() ?? __('siteman::site-health.navigation.icon');
    }

    public static function getNavigationLabel(): string
    {
        return __('siteman::site-health.label');
    }

    public function getHeading(): string|Htmlable
    {
        return __('siteman::site-health.label');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('siteman::site-health.subheading');
    }

    protected function getViewData(): array
    {
        $checkResults = app(ResultStore::class)->latestResults();

        return [
            'lastRanAt' => new Carbon($checkResults?->finishedAt),
            'checkResults' => $checkResults,
        ];
    }

    public function refresh(): void
    {
        Artisan::call(RunHealthChecksCommand::class);

        $this->dispatch('refresh-component');

        Notification::make()
            ->title(__('siteman::site-health.notifications.results_refreshed'))
            ->success()
            ->send();
    }

    protected function getHeaderWidgets(): array
    {
        $results = app(ResultStore::class)->latestResults();

        if (!$results) {
            return [];
        }

        return $results->storedCheckResults
            ->map(function ($result) {
                return new WidgetConfiguration(HealthCheckResultWidget::class, ['result' => $result->toArray()]);
            })
            ->toArray();
    }
}
