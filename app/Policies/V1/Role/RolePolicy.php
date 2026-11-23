<?php

namespace App\Policies\V1\Role;

use App\Enums\V1\Role\UserRole;
use App\Models\V1\User\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->hasRole(UserRole::MANAGER)
            ? Response::allow()
            : Response::deny(
                'شما مجوز مشاهده فهرست موردنظر را ندارید.'
            );
    }
}
