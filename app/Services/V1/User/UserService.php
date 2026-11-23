<?php

namespace App\Services\V1\User;

use App\Enums\V1\Role\UserRole;
use App\Http\Resources\V1\User\Admin\UserCollection;
use App\Http\Resources\V1\User\Admin\UserListCollection;
use App\Models\V1\User\User;
use App\Services\V1\Core\Media\Interfaces\MediaServiceInterface;
use App\Services\V1\Role\Interfaces\RoleServiceInterface;
use App\Services\V1\User\Interfaces\UserServiceInterface;
use App\Traits\V1\Core\Cache\ClearsRedisCache;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    use ClearsRedisCache;

    public function __construct(
        protected RoleServiceInterface  $roleService,
        protected MediaServiceInterface $fileService
    )
    {
    }

    public function index(bool $hasPaginate): array
    {
        $cacheKey = $this->getUsersCacheKey('admin');

        if ($cached = $this->getUsersFromCache($cacheKey)) {
            return $cached;
        }

        $users = $this->getUsers($hasPaginate);

        $this->cacheUsers($cacheKey, $users);

        return $users;
    }

    public function showViaPhone(string $phone): User|Model
    {
        return User::query()
            ->where(
                'phone',
                '=',
                $phone
            )
            ->first();
    }

    public function store(array $inputs): User|Model
    {
        $userAttributes = Arr::only(
            $inputs,
            new User()->getFillable()
        );

        $user = User::query()
            ->create($userAttributes);

        if (!$user) {
            throw new Exception('مشکل در ایجاد کاربر جدید');
        }

        $user->syncRoles(UserRole::WRITER);

        $this->uploadAvatar(
            $user,
            $inputs['avatar'] ?? null
        );

        $this->clearUsersCache();

        return $user;
    }

    public function update(array $inputs, User $user): User|Model
    {
        $userAttributes = Arr::only(
            $inputs,
            new User()->getFillable()
        );

        if (!$user->update($userAttributes)) {
            throw new Exception('مشکل در بروزرسانی کاربر');
        }

        $this->uploadAvatar(
            $user,
            $inputs['avatar'] ?? null
        );

        $this->clearUsersCache();

        return $user;
    }

    public function destroy(User $user): User|Model
    {
        $this->deleteAvatar($user);

        if (!$user->delete()) {
            throw new Exception('مشکل در حذف کاربر');
        }

        $this->clearUsersCache();

        return $user;
    }

    public function changeRole(array $inputs, User $user): User|Model
    {
        $this->clearUsersCache();

        return $this->roleService
            ->changeUserRole($user, $inputs['role']);
    }

    private function getUsers(bool $hasPaginate): array
    {
        $query = User::query()
            ->ignoreRequest(['per_page', 'paginate'])
            ->filter();

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            return UserCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return UserListCollection::make(
            $query->get()
        )->resolve();
    }

    private function getUsersCacheKey(string $cacheContext): string
    {
        $queryParams = request()->query();
        ksort($queryParams);

        return "users:{$cacheContext}:" . md5(
                request()->path() .
                serialize($queryParams)
            );
    }

    private function getUsersFromCache(string $cacheKey): ?array
    {
        $data = Redis::get($cacheKey);

        return $data
            ? json_decode($data, true)
            : null;
    }

    private function cacheUsers(string $cacheKey, array $data): void
    {
        Redis::setex(
            $cacheKey,
            10 * 60,
            json_encode($data)
        );
    }

    private function uploadAvatar(User $user, ?UploadedFile $file): void
    {
        if (!$file) {
            return;
        }

        $this->fileService
            ->attachFile([
                'file' => $file,
                'model' => $user,
                'modelName' => Str::of(User::class)
                    ->classBasename()
                    ->lower()
                    ->value(),
                'subfolder' => 'avatar',
                'mediaTagName' => 'avatar',
                'cacheKey' => 'users',
            ]);
    }

    private function deleteAvatar(User $user): void
    {
        $this->fileService
            ->deleteAttachedFile([
                'model' => $user,
                'mediaTagName' => 'avatar',
            ]);
    }

    private function clearUsersCache(): void
    {
        $this->clearCache('users:*');
        $this->clearCache('medias:*');
        $this->clearCache('comments:*');
        $this->clearCache('posts:*');
        $this->clearCache('categories:*');
        $this->clearCache('tags:*');
    }
}
