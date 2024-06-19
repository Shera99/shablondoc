<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Auth\Access\Response;

class UserSubscriptionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Moderator');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserSubscription $model): bool
    {
        return $user->hasRole('Moderator');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Moderator');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserSubscription $model): bool
    {
        return $user->hasRole('Moderator');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserSubscription $model): bool
    {
        return $user->hasRole('Super-Admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserSubscription $model): bool
    {
        return $user->hasRole('Super-Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserSubscription $model): bool
    {
        return $user->hasRole('Super-Admin');
    }
}
