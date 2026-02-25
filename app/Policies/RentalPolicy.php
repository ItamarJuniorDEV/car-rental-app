<?php

namespace App\Policies;

use App\Models\Rental;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RentalPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Rental $rental)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Rental $rental)
    {
        return true;
    }

    public function delete(User $user, Rental $rental)
    {
        return true;
    }
}
