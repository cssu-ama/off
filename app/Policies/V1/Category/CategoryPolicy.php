<?php

namespace App\Policies\V1\Category;

use App\Enums\V1\Role\UserRole;
use App\Models\V1\Category\Category;
use App\Models\V1\User\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->hasAnyRole([
            UserRole::MANAGER,
            UserRole::ADMIN,
            UserRole::WRITER,
        ])
            ? Response::allow()
            : Response::deny(
                'شما مجوز مشاهده فهرست موردنظر را ندارید.'
            );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Category $category): Response
    {
        if ($user->hasAnyRole([
            UserRole::MANAGER,
            UserRole::ADMIN,
        ])) {
            return Response::allow();
        }

        if ($user->hasRole(UserRole::WRITER)) {
            return $category->user_id === $user->id
                ? Response::allow()
                : Response::deny(
                    'شما مجوز مشاهده این دسته‌بندی را ندارید.'
                );
        }

        return Response::deny(
            'شما مجوز مشاهده این دسته‌بندی را ندارید.'
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->hasAnyRole([
            UserRole::MANAGER,
            UserRole::ADMIN,
            UserRole::WRITER,
        ])
            ? Response::allow()
            : Response::deny(
                'شما مجوز انجام این عملیات را ندارید.'
            );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Category $category): Response
    {
        if ($user->hasAnyRole([
            UserRole::MANAGER,
            UserRole::ADMIN,
        ])) {
            return Response::allow();
        }

        if ($user->hasRole(UserRole::WRITER)) {
            return $category->user_id === $user->id
                ? Response::allow()
                : Response::deny(
                    'شما مجوز انجام این عملیات را ندارید.'
                );
        }

        return Response::deny(
            'شما مجوز انجام این عملیات را ندارید.'
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Category $category): Response
    {
        if ($user->hasAnyRole([
            UserRole::MANAGER,
            UserRole::ADMIN,
        ])) {
            return Response::allow();
        }

        if ($user->hasRole(UserRole::WRITER)) {
            return $category->user_id === $user->id
                ? Response::allow()
                : Response::deny(
                    'شما مجوز انجام این عملیات را ندارید.'
                );
        }

        return Response::deny(
            'شما مجوز انجام این عملیات را ندارید.'
        );
    }
}
