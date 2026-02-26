<?php

namespace App\Policies;

use App\Models\Car;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CarPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Car $car)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Car $car)
    {
        return true;
    }

    public function delete(User $user, Car $car)
    {
        return $user->isAdmin();
    }
}
