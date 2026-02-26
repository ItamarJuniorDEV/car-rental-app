<?php

namespace App\Policies;

use App\Models\Line;
use App\Models\User;

class LinePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Line $line): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Line $line): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Line $line): bool
    {
        return $user->isAdmin();
    }
}
