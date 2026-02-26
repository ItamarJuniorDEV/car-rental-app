<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrandPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Brand $brand)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Brand $brand)
    {
        return true;
    }

    public function delete(User $user, Brand $brand)
    {
        return $user->isAdmin();
    }
}
