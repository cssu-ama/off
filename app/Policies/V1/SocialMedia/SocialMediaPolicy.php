<?php

namespace App\Policies\V1\SocialMedia;

use App\Enums\V1\Role\UserRole;
use App\Models\V1\SocialMedia\SocialMedia;
use App\Models\V1\User\User;
use Illuminate\Auth\Access\Response;

class SocialMediaPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->hasAnyRole([
            UserRole::MANAGER,
            UserRole::ADMIN,
            UserRole::WRITER,
        ])
            ? Response::allow()
            : Response::deny('شما مجوز مشاهده فهرست موردنظر را ندارید.');
    }

    public function view(User $user, SocialMedia $socialMedia): Response
    {
        if ($user->hasAnyRole([UserRole::MANAGER, UserRole::ADMIN])) {
            return Response::allow();
        }

        if ($user->hasRole(UserRole::WRITER)) {
            return $socialMedia->user_id === $user->id
                ? Response::allow()
                : Response::deny('شما مجوز مشاهده این مورد را ندارید.');
        }

        return Response::deny('شما مجوز مشاهده این مورد را ندارید.');
    }

    public function create(User $user): Response
    {
        return $user->hasAnyRole([
            UserRole::MANAGER,
            UserRole::ADMIN,
            UserRole::WRITER,
        ])
            ? Response::allow()
            : Response::deny('شما مجوز انجام این عملیات را ندارید.');
    }

    public function update(User $user, SocialMedia $socialMedia): Response
    {
        if ($user->hasAnyRole([UserRole::MANAGER, UserRole::ADMIN])) {
            return Response::allow();
        }

        if ($user->hasRole(UserRole::WRITER)) {
            return $socialMedia->user_id === $user->id
                ? Response::allow()
                : Response::deny('شما مجوز انجام این عملیات را ندارید.');
        }

        return Response::deny('شما مجوز انجام این عملیات را ندارید.');
    }

    public function delete(User $user, SocialMedia $socialMedia): Response
    {
        if ($user->hasAnyRole([UserRole::MANAGER, UserRole::ADMIN])) {
            return Response::allow();
        }

        if ($user->hasRole(UserRole::WRITER)) {
            return $socialMedia->user_id === $user->id
                ? Response::allow()
                : Response::deny('شما مجوز انجام این عملیات را ندارید.');
        }

        return Response::deny('شما مجوز انجام این عملیات را ندارید.');
    }
}
