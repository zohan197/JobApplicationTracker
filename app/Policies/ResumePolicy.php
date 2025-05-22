<?php

namespace App\Policies;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ResumePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Resume $resume): bool
    {
        return false;
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
    public function update(User $user, Resume $resume): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Resume $resume): bool
    {
        return $user->id === $resume->user_id;
    }
    /**
     * Determine whether the user can restore any models.
     */
    public function download(User $user, Resume $resume)
    {
        return $user->id === $resume->user_id;
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Resume $resume): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Resume $resume): bool
    {
        return false;
    }
}
