<?php declare(strict_types=1);

namespace Siteman\Cms\Resources\Roles\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Arrayable;
use Siteman\Cms\Facades\Siteman;
use Siteman\Cms\Pages\Concerns\IsProtectedPage;
use Spatie\Permission\Contracts\Role;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('role')
                    ->components([
                        TextInput::make('name'),
                    ])
                    ->columnSpan('full'),
                Grid::make()
                    ->columnSpan('full')
                    ->schema([
                        self::getPageSection(),
                        ...self::getResourceSections(),
                    ]),
                TextInput::make('name'),
            ]);
    }

    protected static function getResourceSections(): array
    {
        return Siteman::getResourceInfoForPermissions()
            ->map(function (array $resourceInfo) {
                $permissions = collect(SiteMan::getPermissionsFor($resourceInfo['model']))
                    ->mapWithKeys(function ($permission) use ($resourceInfo) {
                        return [$permission.'_'.$resourceInfo['model_name'] => $permission.'_'.$resourceInfo['model_name']];
                    })
                    ->toArray();

                return Section::make($resourceInfo['model_name'])
                    ->compact()
                    ->collapsible()
                    ->columnSpan('full')
                    ->schema([self::getCheckboxList($resourceInfo['model_name'], $permissions)]);
            }
            )
            ->all();
    }

    public static function getPageSection(): Section
    {
        $permissions = collect(Filament::getPages())
            ->filter(fn ($page) => in_array(IsProtectedPage::class, class_uses_recursive($page)))
            ->mapWithKeys(fn ($page) => [$page::getPermissionName() => $page::getPermissionName()]);

        return Section::make('pages')
            ->compact()
            ->collapsible()
            ->columnSpan('full')
            ->schema([self::getCheckboxList('pages', $permissions)]);
    }

    protected static function getCheckboxList(string $name, array|Arrayable $permissions): CheckboxList
    {
        return CheckboxList::make($name)
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
            });
    }
}
