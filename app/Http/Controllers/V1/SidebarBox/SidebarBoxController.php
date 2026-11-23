<?php

namespace App\Http\Controllers\V1\SidebarBox;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\SidebarBox\SidebarBoxChangeOrderRequest;
use App\Http\Requests\V1\SidebarBox\SidebarBoxPostChangeOrderRequest;
use App\Http\Requests\V1\SidebarBox\SidebarBoxStoreRequest;
use App\Http\Requests\V1\SidebarBox\SidebarBoxUpdateRequest;
use App\Http\Resources\V1\SidebarBox\Admin\SidebarBoxResource;
use App\Http\Resources\V1\SidebarBox\Admin\SidebarPlacementResource;
use App\Http\Resources\V1\SidebarBox\Admin\SidebarPostSelectionTypeResource;
use App\Models\V1\SidebarBox\SidebarBox;
use App\Services\V1\SidebarBox\Interfaces\SidebarBoxServiceInterface;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class SidebarBoxController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected SidebarBoxServiceInterface $sidebarBoxService)
    {
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $this->authorize('viewAny', SidebarBox::class);

            $hasPaginate = request()->query(
                'paginate',
                true
            );

            $data = $this
                ->sidebarBoxService
                ->index($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'جعبه های سایدبار دریافت شدند.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function publicIndex()
    {
        try {
            $hasPaginate = request()->query(
                'paginate',
                true
            );

            $data = $this
                ->sidebarBoxService
                ->publicIndex($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'جعبه های سایدبار دریافت شدند.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function indexPlacement()
    {
        try {
            $this->authorize('viewAnyPlacement', SidebarBox::class);

            $data = $this
                ->sidebarBoxService
                ->indexPlacement();

            return response()->json([
                'success' => true,
                'data' => SidebarPlacementResource::collection($data),
                'message' => 'جایگاه باکس ها با موفقیت دریافت شدند'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function indexPostSelectionType()
    {
        try {
            $this->authorize('viewAnyPostSelectionType', SidebarBox::class);

            $data = $this
                ->sidebarBoxService
                ->indexPostSelectionType();

            return response()->json([
                'success' => true,
                'data' => SidebarPostSelectionTypeResource::collection($data),
                'message' => 'روش های تامین محتوا باکس ها با موفقیت دریافت شدند'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param SidebarBox $sidebarBox
     * @return JsonResponse
     */
    public function show(SidebarBox $sidebarBox): JsonResponse
    {
        try {
            $this->authorize('view', SidebarBox::class);

            return response()->json([
                'success' => true,
                'data' => SidebarBoxResource::make($sidebarBox),
                'message' => 'مورد موردنظر با موفقیت دریافت شد.',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * @param SidebarBoxStoreRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(SidebarBoxStoreRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', SidebarBox::class);

            $inputs = $request->only([
                'title',
                'sort_order',
                'placement',
                'post_selection_type',
                'category_id',
                'tag_id',
                'posts_limit',
                'post_ids'
            ]);

            $inputs['user_id'] = Auth::id();

            $data = $this
                ->sidebarBoxService
                ->store($inputs);

            return response()->json([
                'success' => true,
                'data' => SidebarBoxResource::make($data),
                'message' => 'مورد جدید با موفقیت ایجاد شد.',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * @param SidebarBoxUpdateRequest $request
     * @param SidebarBox $sidebarBox
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(SidebarBoxUpdateRequest $request, SidebarBox $sidebarBox): JsonResponse
    {
        try {
            $this->authorize('update', $sidebarBox);

            $inputs = $request->only([
                'title',
                'sort_order',
                'placement',
                'post_selection_type',
                'category_id',
                'tag_id',
                'posts_limit',
                'post_ids'
            ]);

            $data = $this
                ->sidebarBoxService
                ->update($inputs, $sidebarBox);

            return response()->json([
                'success' => true,
                'data' => SidebarBoxResource::make($data),
                'message' => 'باکس با موفقیت بروز شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param SidebarBoxChangeOrderRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function changeOrder(SidebarBoxChangeOrderRequest $request)
    {
        try {
            $this->authorize('changeOrder', SidebarBox::class);

            $inputs = $request
                ->only(['orders']);

            $this->sidebarBoxService
                ->changeOrder($inputs);

            return response()->json([
                'success' => true,
                'message' => 'ترتیب باکس ها با موفقیت بروز شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param SidebarBoxPostChangeOrderRequest $request
     * @param SidebarBox $sidebarBox
     * @return JsonResponse
     * @throws Throwable
     */
    public function changePostOrder(SidebarBoxPostChangeOrderRequest $request, SidebarBox $sidebarBox)
    {
        try {
            $this->authorize('changePostOrder', SidebarBox::class);

            $inputs = $request
                ->only(['orders']);

            $this->sidebarBoxService
                ->changePostOrder($inputs, $sidebarBox);

            return response()->json([
                'success' => true,
                'message' => 'ترتیب نوشته های باکس با موفقیت بروز شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    public function destroy(SidebarBox $sidebarBox)
    {
        try {
            $this->authorize('delete', $sidebarBox);

            $data = $this
                ->sidebarBoxService
                ->destroy($sidebarBox);

            return response()->json([
                'success' => true,
                'data' => SidebarBoxResource::make($data),
                'message' => 'باکس با موفقیت حذف شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
