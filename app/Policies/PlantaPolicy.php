<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Planta;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlantaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Planta $planta)
    {
        return $user->id === $planta->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Planta $planta)
    {
        return $user->id === $planta->user_id;
    }

    public function delete(User $user, Planta $planta)
    {
        return $user->id === $planta->user_id;
    }

    public function restore(User $user, Planta $planta)
    {
        return $user->id === $planta->user_id;
    }

    public function forceDelete(User $user, Planta $planta)
    {
        return $user->id === $planta->user_id;
    }
}