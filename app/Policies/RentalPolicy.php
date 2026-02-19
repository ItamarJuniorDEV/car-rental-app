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
        //
    }

    public function view(User $user, Rental $rental)
    {
        //
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, Rental $rental)
    {
        //
    }

    public function delete(User $user, Rental $rental)
    {
        //
    }


    public function restore(User $user, Rental $rental)
    {
        //
    }

    public function forceDelete(User $user, Rental $rental)
    {
        //
    }
}
