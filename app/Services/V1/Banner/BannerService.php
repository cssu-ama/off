<?php

namespace App\Services\V1\Banner;

use App\Enums\V1\Banner\BannerPlacement;
use App\Enums\V1\Banner\BannerSize;
use App\Enums\V1\Banner\BannerStatus;
use App\Enums\V1\Banner\LinkRelation;
use App\Http\Resources\V1\Banner\Admin\BannerCollection;
use App\Http\Resources\V1\Banner\Admin\BannerListCollection;
use App\Http\Resources\V1\Banner\Public\PublicBannerCollection;
use App\Http\Resources\V1\Banner\Public\PublicBannerListCollection;
use App\Models\V1\Banner\Banner;
use App\Services\V1\Banner\Interfaces\BannerServiceInterface;
use App\Services\V1\Core\Media\Interfaces\MediaServiceInterface;
use App\Traits\V1\Core\Cache\ClearsRedisCache;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class BannerService implements BannerServiceInterface
{
    use ClearsRedisCache;

    public function __construct(protected MediaServiceInterface $fileService)
    {
    }

    public function index(bool $hasPaginate): array
    {
        $cacheKey = $this->getBannersCacheKey('admin');

        if ($cached = $this->getBannersFromCache($cacheKey)) {
            return $cached;
        }

        $banners = $this->getBanners($hasPaginate);

        $this->cacheBanners($cacheKey, $banners);

        return $banners;
    }

    public function publicIndex(bool $hasPaginate): array
    {
        $cacheKey = $this->getBannersCacheKey('public');

        if ($cached = $this->getBannersFromCache($cacheKey)) {
            return $cached;
        }

        $banners = $this->getPublicBanners($hasPaginate);

        $this->cacheBanners($cacheKey, $banners);

        return $banners;
    }

    public function store(array $inputs): Banner|Model
    {
        $bannerAttributes = Arr::only(
            $inputs,
            new Banner()->getFillable()
        );

        $banner = Banner::query()->create($bannerAttributes);

        if (!$banner) {
            throw new Exception('مشکل در ایجاد مورد جدید.');
        }

        $this->uploadBanner(
            $banner,
            BannerSize::MOBILE->value,
            $inputs['banner_mobile'] ?? null
        );

        $this->uploadBanner(
            $banner,
            BannerSize::TABLET->value,
            $inputs['banner_tablet'] ?? null
        );

        $this->uploadBanner(
            $banner,
            BannerSize::DESKTOP->value,
            $inputs['banner_desktop'] ?? null
        );

        $this->clearBannersCache();

        return $banner;
    }

    public function update(array $inputs, Banner $banner): Banner|Model
    {
        $bannerAttributes = Arr::only(
            $inputs,
            new Banner()->getFillable()
        );

        if (!$banner->update($bannerAttributes)) {
            throw new Exception('مشکل در ایجاد بروزرسانی');
        }

        $this->uploadBanner(
            $banner,
            BannerSize::MOBILE->value,
            $inputs['banner_mobile'] ?? null
        );

        $this->uploadBanner(
            $banner,
            BannerSize::TABLET->value,
            $inputs['banner_tablet'] ?? null
        );

        $this->uploadBanner(
            $banner,
            BannerSize::DESKTOP->value,
            $inputs['banner_desktop'] ?? null
        );

        $this->clearBannersCache();

        return $banner;
    }

    public function indexStatus(): array
    {
        return [
            BannerStatus::INACTIVE,
            BannerStatus::ACTIVE,
        ];
    }

    public function changeStatus(array $inputs, Banner $banner): Banner|Model
    {
        $banner->update([
            'status' => $inputs['status']
        ]);

        $this->clearBannersCache();

        return $banner;
    }

    public function indexPlacement(): array
    {
        return [
            BannerPlacement::NONE,
            BannerPlacement::GLOBAL_TOPBAR,
            BannerPlacement::HOME_CATEGORIES_BOTTOM,
            BannerPlacement::HOME_HERO_TOP,
            BannerPlacement::HOME_LATEST_SIDEBAR,
            BannerPlacement::POST_ABOVE_RELATED_POSTS,
            BannerPlacement::POST_ABOVE_TITLE,
        ];
    }

    public function indexLinkRelation(): array
    {
        return [
            LinkRelation::FOLLOW,
            LinkRelation::NOFOLLOW,
            LinkRelation::SPONSORED,
            LinkRelation::UGC,
        ];
    }

    public function destroy(Banner $banner): Banner|Model
    {
        $this->deleteBanner($banner, BannerSize::MOBILE->value);
        $this->deleteBanner($banner, BannerSize::TABLET->value);
        $this->deleteBanner($banner, BannerSize::DESKTOP->value);

        if (!$banner->delete()) {
            throw new Exception('مشکل در حذف مورد.');
        }

        $this->clearBannersCache();

        return $banner;
    }

    private function getBanners(bool $hasPaginate): array
    {
        $query = Banner::query()
            ->ignoreRequest(['per_page', 'paginate'])
            ->filter();

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            return BannerCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return BannerListCollection::make(
            $query->get()
        )->resolve();
    }

    private function getPublicBanners(bool $hasPaginate): array
    {
        $now = now('UTC');

        $query = Banner::query()
            ->where('status', BannerStatus::ACTIVE)
            ->where('placement', '!=', BannerPlacement::NONE)
            ->where(function ($query) use ($now) {
                $query
                    // Case 1: Both start and end times are specified.
                    ->where(function ($query) use ($now) {
                        $query
                            ->whereNotNull('start_at')
                            ->whereNotNull('end_at')
                            ->where('start_at', '<=', $now)
                            ->where('end_at', '>', $now);
                    })

                    // Case 2: Only the start time is specified.
                    ->orWhere(function ($query) use ($now) {
                        $query
                            ->whereNotNull('start_at')
                            ->whereNull('end_at')
                            ->where('start_at', '<=', $now);
                    })

                    // Case 3: Only the end time is specified.
                    ->orWhere(function ($query) use ($now) {
                        $query
                            ->whereNull('start_at')
                            ->whereNotNull('end_at')
                            ->where('end_at', '>', $now);
                    })

                    // Case 4: No time restrictions are specified.
                    ->orWhere(function ($query) {
                        $query
                            ->whereNull('start_at')
                            ->whereNull('end_at');
                    });
            })
            ->ignoreRequest(['per_page', 'paginate'])
            ->filter();

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            return PublicBannerCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return PublicBannerListCollection::make(
            $query->get()
        )->resolve();
    }

    private function getBannersCacheKey(string $cacheContext): string
    {
        $queryParams = request()->query();
        ksort($queryParams);

        return "banners:{$cacheContext}:" . md5(
                request()->path() .
                serialize($queryParams)
            );
    }

    private function getBannersFromCache(string $cacheKey): ?array
    {
        $data = Redis::get($cacheKey);

        return $data !== null
            ? json_decode($data, true)
            : null;
    }

    private function cacheBanners(string $cacheKey, array $data): void
    {
        Redis::setex(
            $cacheKey,
            10 * 60,
            json_encode($data, JSON_UNESCAPED_UNICODE)
        );
    }

    private function uploadBanner(Banner $banner, string $size, ?UploadedFile $file): void
    {
        if (!$file) {
            return;
        }

        $this->fileService
            ->attachFile([
                'file' => $file,
                'model' => $banner,
                'modelName' => Str::of(Banner::class)
                    ->classBasename()
                    ->lower()
                    ->value(),
                'subfolder' => 'banner',
                'mediaTagName' => 'banner_' . $size,
                'cacheKey' => 'banners',
            ]);
    }

    private function deleteBanner(Banner $banner, string $size): void
    {
        $this->fileService
            ->deleteAttachedFile([
                'model' => $banner,
                'mediaTagName' => 'banner_' . $size,
            ]);
    }

    private function clearBannersCache(): void
    {
        $this->clearCache('medias:*');
        $this->clearCache('banners:*');
    }
}
