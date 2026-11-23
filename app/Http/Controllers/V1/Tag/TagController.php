<?php

namespace App\Http\Controllers\V1\Tag;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Tag\TagStoreRequest;
use App\Http\Requests\V1\Tag\TagUpdateRequest;
use App\Http\Resources\V1\Tag\Admin\TagResource;
use App\Http\Resources\V1\Tag\Public\PublicTagResource;
use App\Models\V1\Tag\Tag;
use App\Services\V1\Tag\Interfaces\TagServiceInterface;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected TagServiceInterface $tagService)
    {
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $this->authorize('viewAny', Tag::class);

            $hasPaginate = request()->query(
                'paginate',
                true
            );

            $data = $this
                ->tagService
                ->index($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'تگ ها دریافت شدند.'
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
                ->tagService
                ->publicIndex($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'تگ ها دریافت شدند.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Tag $tag
     * @return JsonResponse
     */
    public function show(Tag $tag)
    {
        try {
            $this->authorize('view', $tag);

            return response()->json([
                'success' => true,
                'data' => TagResource::make($tag),
                'message' => 'تگ مورد نظر یافت شد'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Tag $tag
     * @return JsonResponse
     */
    public function publicShow(Tag $tag)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => PublicTagResource::make($tag),
                'message' => 'تگ مورد نظر یافت شد'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param TagStoreRequest $request
     * @return JsonResponse
     */
    public function store(TagStoreRequest $request)
    {
        try {
            $this->authorize('create', Tag::class);

            $inputs = $request->only([
                'title',
                'description',
            ]);

            $inputs['user_id'] = Auth::id();

            $data = $this
                ->tagService
                ->store($inputs);

            return response()->json([
                'success' => true,
                'data' => TagResource::make($data),
                'message' => 'تگ با موفقیت ایجاد شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param TagUpdateRequest $request
     * @param Tag $tag
     * @return JsonResponse
     */
    public function update(TagUpdateRequest $request, Tag $tag)
    {
        try {
            $this->authorize('update', $tag);

            $inputs = $request->only([
                'title',
                'description',
            ]);

            $data = $this
                ->tagService
                ->update($inputs, $tag);

            return response()->json([
                'success' => true,
                'data' => TagResource::make($data),
                'message' => 'تگ با موفقیت بروز شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Tag $tag
     * @return JsonResponse
     */
    public function destroy(Tag $tag)
    {
        try {
            $this->authorize('delete', $tag);

            $data = $this
                ->tagService
                ->destroy($tag);

            return response()->json([
                'success' => true,
                'data' => TagResource::make($data),
                'message' => 'تگ با موفقیت حذف شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
