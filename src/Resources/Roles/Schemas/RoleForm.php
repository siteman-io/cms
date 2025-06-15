<?php

namespace Siteman\Cms\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Siteman\Cms\Facades\Siteman;
use Spatie\Permission\Contracts\Role;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        //        dd(self::getModels());
        return $schema
            ->components([
                Section::make('role')
                    ->components([
                        TextInput::make('name'),
                    ])
                    ->columnSpan('full'),
                Grid::make()
                    ->columnSpan('full')
                    ->schema(self::getResourcePermissionFormComponents()),
            ]);
    }

    protected static function getResourcePermissionFormComponents(): array
    {
        return Siteman::getResourceInfoForPermissions()
            ->map(function (array $resourceInfo) {
                $permissions = collect(SiteMan::getPermissionsFor($resourceInfo['model']))
                    ->mapWithKeys(function ($permission) use ($resourceInfo) {
                        return [$permission.'_'.$resourceInfo['model_name'] => $permission.'_'.$resourceInfo['model_name']];
                    })
                    ->toArray();

                return Section::make()
                    ->compact()
                    ->collapsible()
                    ->columnSpan('full')
                    ->schema(
                        [
                            CheckboxList::make($resourceInfo['model_name'])
                                ->label('')
                                ->options($permissions)
                                ->bulkToggleable()
                                ->disabled(fn (Role $record) => Siteman::isSuperAdmin($record))
                                ->columnSpan('full')

                                ->columns([
                                    'sm' => 2,
                                    'lg' => 4,
                                ])
                                ->gridDirection('row')
                                ->afterStateHydrated(function (CheckboxList $component, string $operation, ?Role $record) use ($permissions) {
                                    if (!in_array($operation, ['edit', 'view'])
                                        || blank($record)
                                        || $component->isHidden()
                                        || empty($permissions)) {
                                        return;
                                    }
                                    if (Siteman::isSuperAdmin($record)) {
                                        $component->state(array_keys($permissions));

                                        return;
                                    }

                                    $component->state(
                                        collect($permissions)
                                            ->filter(fn ($value, $key) => $record->checkPermissionTo($key))
                                            ->keys()
                                            ->toArray()
                                    );
                                }),
                        ]
                    );
            }
            )
            ->all();
    }
}
