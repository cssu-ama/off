<?php

namespace App\Http\Controllers\V1\Comment;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Comment\CommentChangeStatusRequest;
use App\Http\Requests\V1\Comment\CommentStoreRequest;
use App\Http\Requests\V1\Comment\ReplayCommentStoreRequest;
use App\Http\Resources\V1\Comment\Admin\CommentResource;
use App\Http\Resources\V1\Comment\Admin\CommentStatusResource;
use App\Http\Resources\V1\Comment\Public\PublicCommentResource;
use App\Models\V1\Comment\Comment;
use App\Services\V1\Comment\Interfaces\CommentServiceInterface;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected CommentServiceInterface $commentService)
    {
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $this->authorize('viewAny', Comment::class);

            $hasPaginate = request()->query(
                'paginate',
                true
            );

            $data = $this
                ->commentService
                ->index($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'نظر ها دریافت شدند.'
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
                ->commentService
                ->publicIndex($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'نظر ها دریافت شدند.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Comment $comment
     * @return JsonResponse
     */
    public function show(Comment $comment)
    {
        try {
            $this->authorize('view', $comment);

            return response()->json([
                'success' => true,
                'data' => CommentResource::make($comment),
                'message' => 'نظر مورد نظر یافت شد'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param CommentStoreRequest $request
     * @return JsonResponse
     */
    public function store(CommentStoreRequest $request)
    {
        try {
            $inputs = $request->only([
                'comment',
                'post_id',
                'author_name',
                'phone',
                'email',
                'title',
            ]);

            $data = $this
                ->commentService
                ->store($inputs);

            return response()->json([
                'success' => true,
                'data' => PublicCommentResource::make($data),
                'message' => 'نظر با موفقیت ایجاد شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param ReplayCommentStoreRequest $request
     * @return JsonResponse
     */
    public function replay(ReplayCommentStoreRequest $request)
    {
        try {
            $inputs = $request->only([
                'comment',
                'post_id',
                'parent_id',
                'author_name',
                'phone',
                'email',
                'title',
            ]);

            $data = $this
                ->commentService
                ->store($inputs);

            return response()->json([
                'success' => true,
                'data' => PublicCommentResource::make($data),
                'message' => 'نظر با موفقیت ایجاد شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    public function indexStatus()
    {
        try {
            $this->authorize('viewAnyStatus', Comment::class);

            $data = $this
                ->commentService
                ->indexStatus();

            return response()->json([
                'success' => true,
                'data' => CommentStatusResource::collection($data),
                'message' => 'وضعیت ها با موفقیت دریافت شدند.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param CommentChangeStatusRequest $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function changeStatus(CommentChangeStatusRequest $request, Comment $comment)
    {
        try {
            $this->authorize('changeStatus', Comment::class);

            $inputs = $request
                ->only(['status']);

            $data = $this
                ->commentService
                ->changeStatus($inputs, $comment);

            return response()->json([
                'success' => true,
                'data' => CommentResource::make($data),
                'message' => 'وضعیت نظر با موفقیت بروز شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Comment $comment
     * @return JsonResponse
     */
    public function destroy(Comment $comment)
    {
        try {
            $this->authorize('delete', $comment);

            $data = $this
                ->commentService
                ->destroy($comment);

            return response()->json([
                'success' => true,
                'data' => CommentResource::make($data),
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
