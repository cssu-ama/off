<?php

namespace App\Services\V1\Role;

use App\Http\Resources\V1\Role\RoleCollection;
use App\Models\V1\Role\Role;
use App\Models\V1\User\User;
use App\Services\V1\Role\Interfaces\RoleServiceInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RoleService.
 */
class RoleService implements RoleServiceInterface
{
    public function index(): array
    {
        $perPage = request()->query(
            'per_page',
            config('eloquentFilter.max_limit', 20)
        );

        $data = Role::query()
            ->ignoreRequest(['per_page'])
            ->filter()
            ->paginate($perPage, ['*'], 'page');

        if (!$data) {
            throw new Exception('نقشی یافت نشد!');
        }

        return
            RoleCollection::make($data)->resolve();
    }

    public function changeUserRole(User $user, string $newRole): User|Model
    {
        $user->syncRoles($newRole);

        return $user;
    }
}
