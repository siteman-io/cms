<?php

namespace Siteman\Cms\Resources\Roles\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Siteman\Cms\Resources\Roles\RoleResource;
use Spatie\Permission\PermissionRegistrar;

/**
 * @property \Spatie\Permission\Models\Role $record
 */
class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public ?Collection $permissions = null;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = collect($data)
            ->filter(function ($permission, $key) {
                return !in_array($key, ['name']);
            })
            ->values()
            ->flatten()
            ->unique();

        return Arr::only($data, ['name']);
    }

    protected function afterSave(): void
    {
        if (!$this->permissions) {
            return;
        }

        $permissionClass = app(PermissionRegistrar::class)->getPermissionClass();
        $permissionModels = $this->permissions->map(fn (string $permission) => $permissionClass::firstOrCreate([
            'name' => $permission,
            'guard_name' => 'web',
        ]));

        $this->record->syncPermissions($permissionModels);
    }
}
