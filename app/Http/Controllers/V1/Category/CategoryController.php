<?php

namespace App\Http\Controllers\V1\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Category\CategoryStoreRequest;
use App\Http\Requests\V1\Category\CategoryUpdateRequest;
use App\Http\Resources\V1\Category\Admin\CategoryResource;
use App\Http\Resources\V1\Category\Public\PublicCategoryResource;
use App\Models\V1\Category\Category;
use App\Services\V1\Category\Interfaces\CategoryServiceInterface;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected CategoryServiceInterface $categoryService)
    {
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $this->authorize('viewAny', Category::class);

            $hasPaginate = request()->query(
                'paginate',
                true
            );

            $data = $this
                ->categoryService
                ->index($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'دسته بندی ها دریافت شدند.'
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
    public function publicIndex()
    {
        try {
            $hasPaginate = request()->query(
                'paginate',
                true
            );

            $data = $this
                ->categoryService
                ->publicIndex($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'دسته بندی ها دریافت شدند.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Category $category
     * @return JsonResponse
     */
    public function show(Category $category)
    {
        try {
            $this->authorize('view', $category);

            return response()->json([
                'success' => true,
                'data' => CategoryResource::make($category),
                'message' => 'دسته بندی مورد نظر یافت شد'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Category $category
     * @return JsonResponse
     */
    public function publicShow(Category $category)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => PublicCategoryResource::make($category),
                'message' => 'دسته بندی مورد نظر یافت شد'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param CategoryStoreRequest $request
     * @return JsonResponse
     */
    public function store(CategoryStoreRequest $request)
    {
        try {
            $this->authorize('create', Category::class);

            $inputs = $request->only([
                'title',
                'description',
                'parent_id'
            ]);

            $inputs['user_id'] = Auth::id();

            $data = $this
                ->categoryService
                ->store($inputs);

            return response()->json([
                'success' => true,
                'data' => CategoryResource::make($data),
                'message' => 'دسته بندی با موفقیت ایجاد شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param CategoryUpdateRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        try {
            $this->authorize('update', $category);

            $inputs = $request->only([
                'title',
                'description',
                'parent_id'
            ]);

            $data = $this
                ->categoryService
                ->update($inputs, $category);

            return response()->json([
                'success' => true,
                'data' => CategoryResource::make($data),
                'message' => 'دسته بندی با موفقیت بروز شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category)
    {
        try {
            $this->authorize('delete', $category);

            $data = $this
                ->categoryService
                ->destroy($category);

            return response()->json([
                'success' => true,
                'data' => CategoryResource::make($data),
                'message' => 'دسته بندی با موفقیت حذف شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
