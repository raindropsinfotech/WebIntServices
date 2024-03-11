<?php

namespace App\Policies;

use App\Models\ExternalProduct;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExternalProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view externalProducts');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExternalProduct $externalProduct): bool
    {
        return $user->can('view externalProducts');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manage externalProducts');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExternalProduct $externalProduct): bool
    {
        return $user->can('manage externalProducts');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExternalProduct $externalProduct): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExternalProduct $externalProduct): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExternalProduct $externalProduct): bool
    {
        return $user->hasRole('admin');
    }
}
