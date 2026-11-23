<?php

namespace App\Services\V1\Role\Interfaces;

use App\Models\V1\User\User;
use Exception;
use Illuminate\Database\Eloquent\Model;

interface RoleServiceInterface
{
    /**
     * @return array
     * @throws Exception
     */
    public function index(): array;

    /**
     * @param User $user
     * @param string $newRole
     * @return User|Model
     */
    public function changeUserRole(User $user, string $newRole): User|Model;
}
