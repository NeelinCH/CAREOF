<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RegistroRiego;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegistroRiegoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, RegistroRiego $registroRiego)
    {
        return $user->id === $registroRiego->tarea->planta->user_id || $user->id === $registroRiego->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, RegistroRiego $registroRiego)
    {
        return $user->id === $registroRiego->tarea->planta->user_id || $user->id === $registroRiego->user_id;
    }

    public function delete(User $user, RegistroRiego $registroRiego)
    {
        return $user->id === $registroRiego->tarea->planta->user_id || $user->id === $registroRiego->user_id;
    }

    public function restore(User $user, RegistroRiego $registroRiego)
    {
        return $user->id === $registroRiego->tarea->planta->user_id;
    }

    public function forceDelete(User $user, RegistroRiego $registroRiego)
    {
        return $user->id === $registroRiego->tarea->planta->user_id;
    }
}