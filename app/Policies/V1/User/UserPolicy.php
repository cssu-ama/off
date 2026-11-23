<?php

namespace App\Policies\V1\User;

use App\Enums\V1\Role\UserRole;
use App\Models\V1\User\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
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

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): Response
    {
        return $user->hasRole(UserRole::MANAGER)
            ? Response::allow()
            : Response::deny(
                'شما مجوز مشاهده این کاربر را ندارید.'
            );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->hasRole(UserRole::MANAGER)
            ? Response::allow()
            : Response::deny(
                'شما مجوز انجام این عملیات را ندارید.'
            );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): Response
    {
        return (
            $user->hasRole(UserRole::MANAGER) ||
            (
                $user->is($model) &&
                (
                    $user->hasRole(UserRole::ADMIN) ||
                    $user->hasRole(UserRole::WRITER)
                )
            )
        )
            ? Response::allow()
            : Response::deny(
                'شما مجوز انجام این عملیات را ندارید.'
            );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): Response
    {
        return
            $user->hasRole(UserRole::MANAGER) &&
            $user->isNot($model)
                ? Response::allow()
                : Response::deny(
                'شما مجوز انجام این عملیات را ندارید.'
            );
    }

    /**
     * Determine whether the user can change the user role.
     */
    public function changeRole(User $user, User $model): Response
    {
        return
            $user->hasRole(UserRole::MANAGER) &&
            $user->isNot($model)
                ? Response::allow()
                : Response::deny(
                'شما مجوز انجام این عملیات را ندارید.'
            );
    }
}
