<?php

namespace App\Policies;

use App\Models\MailSetting;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MailSettingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view mailSettings');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MailSetting $mailSetting): bool
    {
        return $user->can('view mailSettings');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manage mailSettings');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MailSetting $mailSetting): bool
    {
        return $user->can('manage mailSettings');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MailSetting $mailSetting): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MailSetting $mailSetting): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MailSetting $mailSetting): bool
    {
        return $user->hasRole('admin');
    }
}
