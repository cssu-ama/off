<?php

namespace App\Http\Controllers\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\UserChangeRoleRequest;
use App\Http\Requests\V1\User\UserStoreRequest;
use App\Http\Requests\V1\User\UserUpdateRequest;
use App\Http\Resources\V1\User\Admin\UserResource;
use App\Models\V1\User\User;
use App\Services\V1\User\Interfaces\UserServiceInterface;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected UserServiceInterface $userService)
    {
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $this->authorize('viewAny', User::class);

            $hasPaginate = request()->query(
                'paginate',
                true
            );

            $data = $this
                ->userService
                ->index($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'کاربران دریافت شدند.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user)
    {
        try {
            $this->authorize('view', $user);

            return response()->json([
                'success' => true,
                'data' => UserResource::make($user),
                'message' => 'کاربر مورد نظر یافت شد'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param UserStoreRequest $request
     * @return JsonResponse
     */
    public function store(UserStoreRequest $request)
    {
        try {
            $this->authorize('create', User::class);

            $inputs = $request->only([
                'name',
                'phone',
                'avatar',
                'user_name'
            ]);

            $data = $this
                ->userService
                ->store($inputs);

            return response()->json([
                'success' => true,
                'data' => UserResource::make($data),
                'message' => 'کاربر با موفقیت ایجاد شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param UserUpdateRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        try {
            $this->authorize('update', $user);

            $inputs = $request->only([
                'name',
                'phone',
                'avatar',
                'user_name'
            ]);

            $data = $this
                ->userService
                ->update($inputs, $user);

            return response()->json([
                'success' => true,
                'data' => UserResource::make($data),
                'message' => 'کاربر با موفقیت بروز شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user)
    {
        try {
            $this->authorize('delete', $user);

            $data = $this
                ->userService
                ->destroy($user);

            return response()->json([
                'success' => true,
                'data' => UserResource::make($data),
                'message' => 'کاربر با موفقیت حذف شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param UserChangeRoleRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function changeRole(UserChangeRoleRequest $request, User $user)
    {
        try {
            $this->authorize('changeRole', $user);

            $inputs = $request
                ->only(['role']);

            $data = $this
                ->userService
                ->changeRole($inputs, $user);

            return response()->json([
                'success' => true,
                'data' => UserResource::make($data),
                'message' => 'نقش کاربر با موفقیت بروز شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
