<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tarea;
use Illuminate\Auth\Access\HandlesAuthorization;

class TareaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Tarea $tarea)
    {
        return $user->id === $tarea->planta->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Tarea $tarea)
    {
        return $user->id === $tarea->planta->user_id;
    }

    public function delete(User $user, Tarea $tarea)
    {
        return $user->id === $tarea->planta->user_id;
    }

    public function restore(User $user, Tarea $tarea)
    {
        return $user->id === $tarea->planta->user_id;
    }

    public function forceDelete(User $user, Tarea $tarea)
    {
        return $user->id === $tarea->planta->user_id;
    }
}