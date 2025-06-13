<?php
declare(strict_types=1);

namespace Siteman\Cms\Resources\MenuResource;

use Filament\Actions\Action;
use Filament\Forms\Components;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\View;
use Filament\Support\Enums\Width;
use Illuminate\Support\Collection;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Models\Menu;
use Siteman\Cms\Models\MenuLocation;

trait HasLocationAction
{
    protected ?Collection $menus = null;

    protected ?Collection $menuLocations = null;

    public function getLocationAction(): Action
    {
        return Action::make('locations')
            ->label(__('siteman::menu.resource.actions.locations.label'))
            ->modalHeading(__('siteman::menu.resource.actions.locations.heading'))
            ->modalDescription(__('siteman::menu.resource.actions.locations.description'))
            ->modalSubmitActionLabel(__('siteman::menu.resource.actions.locations.submit'))
            ->modalWidth(Width::Large)
            ->modalSubmitAction($this->getRegisteredLocations()->isEmpty() ? false : null)
            ->color('gray')
            ->fillForm(fn () => $this->getRegisteredLocations()->map(fn ($location, $key) => [
                'location' => $location,
                'menu' => $this->getMenuLocations()->where('location', $key)->first()?->menu_id,
            ])->all())
            ->action(function (array $data) {
                $locations = collect($data)
                    ->map(fn ($item) => $item['menu'] ?? null)
                    ->all();

                $this->getMenuLocations()
                    ->pluck('location')
                    ->diff($this->getRegisteredLocations()->keys())
                    ->each(fn ($location) => $this->getMenuLocations()->where('location', $location)->each->delete());

                foreach ($locations as $location => $menu) {
                    if (!$menu) {
                        $this->getMenuLocations()->where('location', $location)->each->delete();

                        continue;
                    }

                    MenuLocation::updateOrCreate(
                        ['location' => $location],
                        ['menu_id' => $menu],
                    );
                }

                Notification::make()
                    ->title(__('siteman::menu.resource.notifications.locations.title'))
                    ->success()
                    ->send();
            })
            ->schema($this->getRegisteredLocations()->map(
                fn ($location, $key) => Grid::make(2)
                    ->statePath($key)
                    ->schema([
                        TextInput::make('location')
                            ->label(__('siteman::menu.resource.actions.locations.form.location.label'))
                            ->hiddenLabel($key !== $this->getRegisteredLocations()->keys()->first())
                            ->disabled(),

                        Select::make('menu')
                            ->label(__('siteman::menu.resource.actions.locations.form.menu.label'))
                            ->searchable()
                            ->hiddenLabel($key !== $this->getRegisteredLocations()->keys()->first())
                            ->options($this->getMenus()->pluck('name', 'id')->all()),
                    ]),
            )->all() ?: [
                //                View::make('filament-tables::components.empty-state.index')
                //                    ->viewData([
                //                        'heading' => __('siteman::menu.resource.actions.locations.empty.heading'),
                //                        'icon' => 'heroicon-o-x-mark',
                //                    ]),
            ]);
    }

    protected function getMenus(): Collection
    {
        return $this->menus ??= Menu::all();
    }

    protected function getMenuLocations(): Collection
    {
        return $this->menuLocations ??= MenuLocation::all();
    }

    protected function getRegisteredLocations(): Collection
    {
        return collect(Siteman::getMenuLocations());
    }
}
