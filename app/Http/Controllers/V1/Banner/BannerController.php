<?php

namespace App\Http\Controllers\V1\Banner;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Banner\BannerChangeStatusRequest;
use App\Http\Requests\V1\Banner\BannerStoreRequest;
use App\Http\Requests\V1\Banner\BannerUpdateRequest;
use App\Http\Resources\V1\Banner\Admin\BannerLinkRelationResource;
use App\Http\Resources\V1\Banner\Admin\BannerPlacementResource;
use App\Http\Resources\V1\Banner\Admin\BannerResource;
use App\Http\Resources\V1\Banner\Admin\BannerStatusResource;
use App\Models\V1\Banner\Banner;
use App\Services\V1\Banner\Interfaces\BannerServiceInterface;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected BannerServiceInterface $bannerService)
    {
    }

    public function index(): JsonResponse
    {
        try {
            $this->authorize('viewAny', Banner::class);

            $hasPaginate = request()->query(
                'paginate',
                true
            );

            $data = $this
                ->bannerService
                ->index($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'بنر ها دریافت شدند.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function publicIndex(): JsonResponse
    {
        try {
            $hasPaginate = request()->query(
                'paginate',
                true
            );

            $data = $this
                ->bannerService
                ->publicIndex($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'بنر ها دریافت شدند.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function show(Banner $banner): JsonResponse
    {
        try {
            $this->authorize('view', Banner::class);

            return response()->json([
                'success' => true,
                'data' => BannerResource::make($banner),
                'message' => 'مورد موردنظر با موفقیت دریافت شد.',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function store(BannerStoreRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', Banner::class);

            $inputs = $request->only([
                'title',
                'link',
                'placement',
                'rel',
                'status',
                'banner_mobile',
                'banner_tablet',
                'banner_desktop',
                'start_at',
                'end_at',
            ]);

            $inputs['user_id'] = Auth::id();

            $data = $this
                ->bannerService
                ->store($inputs);

            return response()->json([
                'success' => true,
                'data' => BannerResource::make($data),
                'message' => 'مورد جدید با موفقیت ایجاد شد.',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function update(BannerUpdateRequest $request, Banner $banner): JsonResponse
    {
        try {
            $this->authorize('update', Banner::class);

            $inputs = $request->only([
                'title',
                'link',
                'placement',
                'rel',
                'status',
                'banner_mobile',
                'banner_tablet',
                'banner_desktop',
                'start_at',
                'end_at',
            ]);

            $data = $this->bannerService->update(
                $inputs,
                $banner
            );

            return response()->json([
                'success' => true,
                'data' => BannerResource::make($data),
                'message' => 'مورد موردنظر با موفقیت بروزرسانی شد.',
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
    public function indexStatus()
    {
        try {
            $this->authorize('viewAnyStatus', Banner::class);

            $data = $this
                ->bannerService
                ->indexStatus();

            return response()->json([
                'success' => true,
                'data' => BannerStatusResource::collection($data),
                'message' => 'وضعیت بنر ها با موفقیت دریافت شدند'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param BannerChangeStatusRequest $request
     * @param Banner $banner
     * @return JsonResponse
     */
    public function changeStatus(BannerChangeStatusRequest $request, Banner $banner)
    {
        try {
            $this->authorize('changeStatus', Banner::class);

            $inputs = $request
                ->only(['status']);

            $data = $this
                ->bannerService
                ->changeStatus($inputs, $banner);

            return response()->json([
                'success' => true,
                'data' => BannerResource::make($data),
                'message' => 'وضعیت بنر با موفقیت بروز شد'
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
            $this->authorize('viewAnyPlacement', Banner::class);

            $data = $this
                ->bannerService
                ->indexPlacement();

            return response()->json([
                'success' => true,
                'data' => BannerPlacementResource::collection($data),
                'message' => 'جایگاه بنر ها با موفقیت دریافت شدند'
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
    public function indexLinkRelation()
    {
        try {
            $this->authorize('viewAnyLinkRelation', Banner::class);

            $data = $this
                ->bannerService
                ->indexLinkRelation();

            return response()->json([
                'success' => true,
                'data' => BannerLinkRelationResource::collection($data),
                'message' => 'نوع لینک بنر ها با موفقیت دریافت شدند'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    public function destroy(Banner $banner): JsonResponse
    {
        try {
            $this->authorize('delete', Banner::class);
            $data = $this->bannerService->destroy($banner);

            return response()->json([
                'success' => true,
                'data' => BannerResource::make($data),
                'message' => 'مورد موردنظر با موفقیت حذف شد.',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
