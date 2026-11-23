<?php

namespace App\Http\Controllers\V1\Role;

use App\Http\Controllers\Controller;
use App\Models\V1\Role\Role;
use App\Services\V1\Role\Interfaces\RoleServiceInterface;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected RoleServiceInterface $roleService)
    {
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $this->authorize('viewAny', Role::class);

            $data = $this
                ->roleService
                ->index();

            return response()->json($data);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
