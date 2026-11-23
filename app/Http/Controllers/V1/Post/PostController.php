<?php

namespace App\Http\Controllers\V1\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Post\PostChangeOrderRequest;
use App\Http\Requests\V1\Post\PostChangeStatusRequest;
use App\Http\Requests\V1\Post\PostStoreRequest;
use App\Http\Requests\V1\Post\PostUpdateRequest;
use App\Http\Resources\V1\Post\Admin\PostResource;
use App\Http\Resources\V1\Post\Admin\PostStatusResource;
use App\Http\Resources\V1\Post\Public\PublicPostResource;
use App\Models\V1\Post\Post;
use App\Services\V1\Post\Interfaces\PostServiceInterface;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Throwable;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected PostServiceInterface $postService)
    {
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $this->authorize('viewAny', Post::class);

            $hasPaginate = request()->query(
                'paginate',
                true
            );

            $data = $this
                ->postService
                ->index($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'پست ها دریافت شدند.'
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
                ->postService
                ->publicIndex($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'پست ها دریافت شدند.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Post $post
     * @return JsonResponse
     */
    public function show(Post $post)
    {
        try {
            $this->authorize('view', $post);

            return response()->json([
                'success' => true,
                'data' => PostResource::make($post),
                'message' => 'پست مورد نظر یافت شد'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Post $post
     * @return JsonResponse
     */
    public function publicShow(Post $post)
    {
        try {
            $data = $this
                ->postService
                ->show($post);

            return response()->json([
                'success' => true,
                'data' => PublicPostResource::make($data),
                'message' => 'پست مورد نظر یافت شد'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param PostStoreRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(PostStoreRequest $request)
    {
        try {
            $this->authorize('create', Post::class);

            $inputs = $request->only([
                'title',
                'description',
                'content',
                'status',
                'thumbnail',
                'category_ids',
                'tag_ids',
                'is_top_headline',
                'is_headline',
                'is_specific',
                'order'
            ]);

            $inputs['user_id'] = Auth::id();

            $data = $this
                ->postService
                ->store($inputs);

            return response()->json([
                'success' => true,
                'data' => PostResource::make($data),
                'message' => 'پست با موفقیت ایجاد شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param PostUpdateRequest $request
     * @param Post $post
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        try {
            $this->authorize('update', $post);

            $inputs = $request->only([
                'title',
                'description',
                'content',
                'status',
                'thumbnail',
                'category_ids',
                'tag_ids',
                'is_top_headline',
                'is_headline',
                'is_specific',
                'order'
            ]);

            $data = $this
                ->postService
                ->update($inputs, $post);

            return response()->json([
                'success' => true,
                'data' => PostResource::make($data),
                'message' => 'پست با موفقیت بروز شد'
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
    public function indexStatus()
    {
        try {
            $this->authorize('viewAnyStatus', Post::class);

            $data = $this
                ->postService
                ->indexStatus();

            return response()->json([
                'success' => true,
                'data' => PostStatusResource::collection($data),
                'message' => 'وضعیت پست ها با موفقیت دریافت شدند'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param PostChangeStatusRequest $request
     * @param Post $post
     * @return JsonResponse
     */
    public function changeStatus(PostChangeStatusRequest $request, Post $post)
    {
        try {
            $this->authorize('changeStatus', $post);

            $inputs = $request
                ->only(['status']);

            $data = $this
                ->postService
                ->changeStatus($inputs, $post);

            return response()->json([
                'success' => true,
                'data' => PostResource::make($data),
                'message' => 'وضعیت پست با موفقیت بروز شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param PostChangeOrderRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function changeOrder(PostChangeOrderRequest $request)
    {
        try {
            $this->authorize('changeOrder', Post::class);

            $inputs = $request
                ->only(['orders']);

            $this->postService
                ->changeOrder($inputs);

            return response()->json([
                'success' => true,
                'message' => 'ترتیب پست ها با موفقیت بروز شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Post $post
     * @return JsonResponse
     */
    public function destroy(Post $post)
    {
        try {
            $this->authorize('delete', $post);

            $data = $this
                ->postService
                ->destroy($post);

            return response()->json([
                'success' => true,
                'data' => PostResource::make($data),
                'message' => 'پست با موفقیت حذف شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
