<?php

namespace Siteman\Cms\Policies;

use Illuminate\Foundation\Auth\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_role');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('view_role');
    }

    public function create(User $user): bool
    {
        return $user->can('create_role');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can('update_role');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->can('delete_role');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_role');
    }
}
