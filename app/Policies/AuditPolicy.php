<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use OwenIt\Auditing\Models\Audit as ModelsAudit;

class AuditPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view audit');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ModelsAudit $audit): bool
    {
        return $user->can('view audit');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ModelsAudit $audit): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ModelsAudit $audit): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ModelsAudit $audit): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ModelsAudit $audit): bool
    {
        return $user->hasRole('admin');
    }
}
