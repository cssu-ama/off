<?php

namespace App\Services\V1\User\Interfaces;

use App\Models\V1\User\User;
use Exception;
use Illuminate\Database\Eloquent\Model;

interface UserServiceInterface
{
    /**
     * @param bool $hasPaginate
     * @return array
     * @throws Exception
     */
    public function index(bool $hasPaginate): array;

    /**
     * @param string $phone
     * @return User|Model
     */
    public function showViaPhone(string $phone): User|Model;

    /**
     * @param array $inputs
     * @return User|Model
     * @throws Exception
     */
    public function store(array $inputs): User|Model;

    /**
     * @param array $inputs
     * @param User $user
     * @return User|Model
     * @throws Exception
     */
    public function update(array $inputs, User $user): User|Model;

    /**
     * @param User $user
     * @return User|Model
     * @throws Exception
     */
    public function destroy(User $user): User|Model;

    /**
     * @param array $inputs
     * @param User $user
     * @return User|Model
     */
    public function changeRole(array $inputs, User $user): User|Model;
}
