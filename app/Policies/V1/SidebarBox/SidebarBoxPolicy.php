<?php

namespace App\Policies\V1\SidebarBox;

use App\Enums\V1\Role\UserRole;
use App\Models\V1\SidebarBox\SidebarBox;
use App\Models\V1\User\User;
use Illuminate\Auth\Access\Response;

class SidebarBoxPolicy
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
    public function view(User $user): Response
    {
        return $user->hasAnyRole([
            UserRole::MANAGER,
            UserRole::ADMIN,
        ])
            ? Response::allow()
            : Response::deny(
                'شما مجوز مشاهده این مورد را ندارید.'
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
        ])
            ? Response::allow()
            : Response::deny(
                'شما مجوز انجام این عملیات را ندارید.'
            );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): Response
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
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SidebarBox $sidebarBox): Response
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
     * Determine whether the user can change order the model.
     */
    public function changeOrder(User $user): Response
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
     * Determine whether the user can change post order the model.
     */
    public function changePostOrder(User $user): Response
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
     * Determine whether the user can view posts selection types the model.
     */
    public function viewAnyPostSelectionType(User $user): Response
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
     * Determine whether the user can view placements the model.
     */
    public function viewAnyPlacement(User $user): Response
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
}
