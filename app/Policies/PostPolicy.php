<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     * This allows authenticated users to view their own posts.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Users can only view their own posts.
     */
    public function view(User $user, Post $post): bool
    {
        // return true;
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can create models.
     * Any authenticated user can create posts.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Users can only update their own posts.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * Users can only delete their own posts.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     * Users can only restore their own posts.
     */
    public function restore(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * Users can only permanently delete their own posts.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }
}
