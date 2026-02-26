<?php

namespace App\Policies;

use App\Models\Line;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LinePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Line $line)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Line $line)
    {
        return true;
    }

    public function delete(User $user, Line $line)
    {
        return true;
    }
}
