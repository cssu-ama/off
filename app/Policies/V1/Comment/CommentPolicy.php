<?php

namespace App\Policies\V1\Comment;

use App\Enums\V1\Role\UserRole;
use App\Models\V1\Comment\Comment;
use App\Models\V1\User\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->hasAnyRole([
            UserRole::MANAGER,
            UserRole::ADMIN,
        ])
            ? Response::allow()
            : Response::deny(
                'شما مجوز مشاهده فهرست موردنظر را ندارید.'
            );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Comment $comment): Response
    {
        return $user->hasAnyRole([
            UserRole::MANAGER,
            UserRole::ADMIN,
        ])
            ? Response::allow()
            : Response::deny(
                'شما مجوز مشاهده این کامنت را ندارید.'
            );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): Response
    {
        return $user->hasAnyRole([
            UserRole::MANAGER,
            UserRole::ADMIN,
        ])
            ? Response::allow()
            : Response::deny(
                'شما مجوز انجام این عملیات را ندارید.'
            );
    }

    /**
     * Determine whether the user can view any models' status.
     */
    public function viewAnyStatus(User $user): Response
    {
        return $user->hasAnyRole([
            UserRole::MANAGER,
            UserRole::ADMIN,
        ])
            ? Response::allow()
            : Response::deny(
                'شما مجوز مشاهده فهرست موردنظر را ندارید.'
            );
    }

    /**
     * Determine whether the user can change the comment status.
     */
    public function changeStatus(User $user): Response
    {
        return $user->hasAnyRole([
            UserRole::MANAGER,
            UserRole::ADMIN,
        ])
            ? Response::allow()
            : Response::deny(
                'شما مجوز انجام این عملیات را ندارید.'
            );
    }
}
