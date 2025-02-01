<?php

namespace Siteman\Cms\Policies;

use Siteman\Cms\Models\Page;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class PagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_page');
    }

    public function view(User $user, Page $page): bool
    {
        return $user->can('view_page');
    }

    public function create(User $user): bool
    {
        return $user->can('create_page');
    }

    public function update(User $user, Page $page): bool
    {
        return $user->can('update_page');
    }

    public function delete(User $user, Page $page): bool
    {
        return $user->can('delete_page');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_page');
    }

    public function forceDelete(User $user, Page $page): bool
    {
        return $user->can('force_delete_page');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_page');
    }

    public function restore(User $user, Page $page): bool
    {
        return $user->can('restore_page');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_page');
    }

    public function replicate(User $user, Page $page): bool
    {
        return $user->can('replicate_page');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_page');
    }
}
