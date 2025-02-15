<?php

namespace Siteman\Cms\Policies;

use Illuminate\Foundation\Auth\User;
use Siteman\Cms\Models\Post;

class PostPolicy
{
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
}
