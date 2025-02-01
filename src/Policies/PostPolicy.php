<?php

namespace Siteman\Cms\Policies;

use Siteman\Cms\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_post');
    }

    public function view(User $user, Post $post): bool
    {
        return $user->can('view_post');
    }

    public function create(User $user): bool
    {
        return $user->can('create_post');
    }

    public function update(User $user, Post $post): bool
    {
        return $user->can('update_post');
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->can('delete_post');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_post');
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return $user->can('force_delete_post');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_post');
    }

    public function restore(User $user, Post $post): bool
    {
        return $user->can('restore_post');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_post');
    }

    public function replicate(User $user, Post $post): bool
    {
        return $user->can('replicate_post');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_post');
    }
}
