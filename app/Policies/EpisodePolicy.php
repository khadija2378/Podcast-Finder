<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Épisode;
use Illuminate\Auth\Access\Response;

class EpisodePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Épisode $Épisode): bool
    {
         return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
         return $user->role === 'admin' || $user->role === 'animateur';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Épisode $Épisode): bool
    {
        return $user->id === $Épisode->podcast->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Épisode $Épisode): bool
    {
        if($user->role === 'admin'){
            return true;
        }
        return $user->id === $Épisode->podcast->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Épisode $Épisode): bool
    {
         return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Épisode $Épisode): bool
    {
         return true;
    }
}
