<?php

namespace App\Http\Controllers\V1\Core\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Core\File\UploadFileRequest;
use App\Http\Resources\V1\Core\File\MediaResource;
use App\Http\Resources\V1\Core\File\MediaTypesResource;
use App\Models\V1\Core\Media\Media;
use App\Services\V1\Core\Media\MediaService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class MediaController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected MediaService $mediaService)
    {
    }

    /**
     * @return JsonResponse
     */
    public function index()
    {
        try {
            $this->authorize('viewAny', Media::class);

            $hasPaginate = request()->query(
                'paginate',
                true
            );

            $data = $this
                ->mediaService
                ->index($hasPaginate);

            return response()->json($data);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Media $media
     * @return JsonResponse
     */
    public function show(Media $media)
    {
        try {
            $this->authorize('view', $media);

            return response()->json([
                'success' => true,
                'data' => MediaResource::make($media),
                'message' => 'فایل مورد نظر یافت شد'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    public function store(UploadFileRequest $request)
    {
        try {
            $this->authorize('create', Media::class);

            $inputs = $request->only(['file', 'alt']);
            $inputs['subfolder'] = 'medias';

            $data = $this->mediaService
                ->store($inputs);

            return response()->json([
                'success' => true,
                'data' => MediaResource::make($data),
                'message' => 'فایل در حال بارگزاری است، لطفا چند لحظه دیگر مراجعه کنید...'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param Media $media
     * @return JsonResponse
     */
    public function destroy(Media $media)
    {
        try {
            $this->authorize('delete', $media);

            $data = $this
                ->mediaService
                ->destroy($media);

            return response()->json([
                'success' => true,
                'data' => MediaResource::make($data),
                'message' => 'فایل مورد نظر با موفقیت حذف شد'
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
    public function indexTypes()
    {
        try {
            $this->authorize('viewAnyTypes', Media::class);

            $data = $this
                ->mediaService
                ->indexTypes();

            return response()->json([
                'success' => true,
                'data' => MediaTypesResource::collection($data),
                'message' => 'انواع داده برای فایل دریافت شد'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
