<?php

namespace App\Policies\V1\Banner;

use App\Enums\V1\Role\UserRole;
use App\Models\V1\User\User;
use Illuminate\Auth\Access\Response;

class BannerPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasRole(UserRole::MANAGER)
            ? Response::allow()
            : Response::deny(
                'شما مجوز مشاهده فهرست موردنظر را ندارید.'
            );
    }

    public function view(User $user): Response
    {
        return $user->hasRole(UserRole::MANAGER)
            ? Response::allow()
            : Response::deny(
                'شما مجوز مشاهده این مورد را ندارید.'
            );
    }

    public function create(User $user): Response
    {
        return $user->hasRole(UserRole::MANAGER)
            ? Response::allow()
            : Response::deny(
                'شما مجوز انجام این عملیات را ندارید.'
            );
    }

    public function update(User $user): Response
    {
        return $user->hasRole(UserRole::MANAGER)
            ? Response::allow()
            : Response::deny(
                'شما مجوز انجام این عملیات را ندارید.'
            );
    }

    public function viewAnyStatus(User $user): Response
    {
        return $user->hasRole(UserRole::MANAGER)
            ? Response::allow()
            : Response::deny(
                'شما مجوز مشاهده فهرست موردنظر را ندارید.'
            );
    }

    public function changeStatus(User $user): Response
    {
        return $user->hasRole(UserRole::MANAGER)
            ? Response::allow()
            : Response::deny(
                'شما مجوز انجام این عملیات را ندارید.'
            );
    }

    public function viewAnyPlacement(User $user): Response
    {
        return $user->hasRole(UserRole::MANAGER)
            ? Response::allow()
            : Response::deny(
                'شما مجوز مشاهده فهرست موردنظر را ندارید.'
            );
    }

    public function viewAnyLinkRelation(User $user): Response
    {
        return $user->hasRole(UserRole::MANAGER)
            ? Response::allow()
            : Response::deny(
                'شما مجوز مشاهده فهرست موردنظر را ندارید.'
            );
    }

    public function delete(User $user): Response
    {
        return $user->hasRole(UserRole::MANAGER)
            ? Response::allow()
            : Response::deny(
                'شما مجوز انجام این عملیات را ندارید.'
            );
    }
}
