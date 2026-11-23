<?php

namespace App\Http\Controllers\V1\SocialMedia;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\SocialMedia\SocialMediaStoreRequest;
use App\Http\Requests\V1\SocialMedia\SocialMediaUpdateRequest;
use App\Http\Resources\V1\SocialMedia\Admin\SocialMediaCollection;
use App\Http\Resources\V1\SocialMedia\Admin\SocialMediaResource;
use App\Http\Resources\V1\SocialMedia\Public\PublicSocialMediaCollection;
use App\Http\Resources\V1\SocialMedia\Public\PublicSocialMediaResource;
use App\Models\V1\SocialMedia\SocialMedia;
use App\Services\V1\SocialMedia\Interfaces\SocialMediaServiceInterface;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SocialMediaController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected SocialMediaServiceInterface $socialMediaService)
    {
    }

    public function index(): JsonResponse
    {
        try {
            $this->authorize('viewAny', SocialMedia::class);

            $hasPaginate = request()->query(
                'paginate',
                true
            );

            $data = $this
                ->socialMediaService
                ->index($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'شبکه های اجتماعی دریافت شدند.'
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
                ->socialMediaService
                ->publicIndex($hasPaginate);

            return response()->json([
                'success' => true,
                ...$data,
                'message' => 'شبکه های اجتماعی دریافت شدند.'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function show(SocialMedia $socialMedia): JsonResponse
    {
        try {
            $this->authorize('view', $socialMedia);

            return response()->json([
                'success' => true,
                'data' => SocialMediaResource::make($socialMedia),
                'message' => 'مورد موردنظر با موفقیت دریافت شد.',
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function store(SocialMediaStoreRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', SocialMedia::class);

            $inputs = $request->only([
                'title',
                'link',
                'icon'
            ]);

            $inputs['user_id'] = Auth::id();

            $data = $this->socialMediaService->store($inputs);

            return response()->json([
                'success' => true,
                'data' => SocialMediaResource::make($data),
                'message' => 'مورد جدید با موفقیت ایجاد شد.',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function update(SocialMediaUpdateRequest $request, SocialMedia $socialMedia): JsonResponse
    {
        try {
            $this->authorize('update', $socialMedia);

            $inputs = $request->only([
                'title',
                'link',
                'icon'
            ]);

            $data = $this->socialMediaService->update(
                $inputs,
                $socialMedia
            );

            return response()->json([
                'success' => true,
                'data' => SocialMediaResource::make($data),
                'message' => 'مورد موردنظر با موفقیت بروزرسانی شد.',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function destroy(SocialMedia $socialMedia): JsonResponse
    {
        try {
            $this->authorize('delete', $socialMedia);
            $data = $this->socialMediaService->destroy($socialMedia);

            return response()->json([
                'success' => true,
                'data' => SocialMediaResource::make($data),
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
