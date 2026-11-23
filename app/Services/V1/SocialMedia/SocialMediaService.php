<?php

namespace App\Services\V1\SocialMedia;

use App\Enums\V1\Role\UserRole;
use App\Http\Resources\V1\SocialMedia\Admin\SocialMediaCollection;
use App\Http\Resources\V1\SocialMedia\Admin\SocialMediaListCollection;
use App\Http\Resources\V1\SocialMedia\Public\PublicSocialMediaCollection;
use App\Http\Resources\V1\SocialMedia\Public\PublicSocialMediaListCollection;
use App\Models\V1\SocialMedia\SocialMedia;
use App\Services\V1\Core\Media\Interfaces\MediaServiceInterface;
use App\Services\V1\SocialMedia\Interfaces\SocialMediaServiceInterface;
use App\Traits\V1\Core\Cache\ClearsRedisCache;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class SocialMediaService implements SocialMediaServiceInterface
{
    use ClearsRedisCache;

    public function __construct(protected MediaServiceInterface $fileService)
    {
    }


    public function index(bool $hasPaginate): array
    {
        $cacheKey = $this->getSocialMediasCacheKey('admin');

        if ($cached = $this->getSocialMediasFromCache($cacheKey)) {
            return $cached;
        }

        $socialMedias = $this->getSocialMedias($hasPaginate);

        $this->cacheSocialMedias($cacheKey, $socialMedias);

        return $socialMedias;
    }

    public function publicIndex(bool $hasPaginate): array
    {
        $cacheKey = $this->getSocialMediasCacheKey('public');

        if ($cached = $this->getSocialMediasFromCache($cacheKey)) {
            return $cached;
        }

        $socialMedias = $this->getPublicSocialMedias($hasPaginate);

        $this->cacheSocialMedias($cacheKey, $socialMedias);

        return $socialMedias;
    }

    public function store(array $inputs): SocialMedia|Model
    {
        $socialMediaAttributes = Arr::only(
            $inputs,
            new SocialMedia()->getFillable()
        );

        $socialMedia = SocialMedia::query()->create($socialMediaAttributes);

        if (!$socialMedia) {
            throw new Exception('مشکل در ایجاد مورد جدید.');
        }

        $this->uploadIcon(
            $socialMedia,
            $inputs['icon'] ?? null
        );

        $this->clearSocialMediasCache();

        return $socialMedia;
    }

    public function update(array $inputs, SocialMedia $socialMedia): SocialMedia|Model
    {
        $socialMediaAttributes = Arr::only(
            $inputs,
            new SocialMedia()->getFillable()
        );


        if (!$socialMedia->update($socialMediaAttributes)) {
            throw new Exception('مشکل در بروزرسانی مورد.');
        }

        $this->uploadIcon(
            $socialMedia,
            $inputs['icon'] ?? null
        );

        $this->clearSocialMediasCache();

        return $socialMedia->refresh();
    }

    public function destroy(SocialMedia $socialMedia): SocialMedia|Model
    {
        $this->deleteIcon($socialMedia);

        if (!$socialMedia->delete()) {
            throw new Exception('مشکل در حذف مورد.');
        }

        $this->clearSocialMediasCache();

        return $socialMedia;
    }

    private function getSocialMedias(bool $hasPaginate): array
    {
        $query = SocialMedia::query()
            ->when(
                !Auth::user()->hasAnyRole([
                    UserRole::ADMIN,
                    UserRole::MANAGER,
                ]),
                fn($query) => $query->where(
                    'social_medias.user_id',
                    '=',
                    Auth::id()
                )
            )
            ->ignoreRequest(['per_page', 'paginate'])
            ->filter();

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            return SocialMediaCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return SocialMediaListCollection::make(
            $query->get()
        )->resolve();
    }

    private function getPublicSocialMedias(bool $hasPaginate): array
    {
        $query = SocialMedia::query()
            ->ignoreRequest(['per_page', 'paginate'])
            ->filter();

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            return PublicSocialMediaCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return PublicSocialMediaListCollection::make(
            $query->get()
        )->resolve();
    }

    private function getSocialMediasCacheKey(string $cacheContext): string
    {
        $queryParams = request()->query();
        ksort($queryParams);

        return "social_medias:{$cacheContext}:" . md5(
                request()->path() .
                serialize($queryParams)
            );
    }

    private function getSocialMediasFromCache(string $cacheKey): ?array
    {
        $data = Redis::get($cacheKey);

        return $data !== null
            ? json_decode($data, true)
            : null;
    }

    private function cacheSocialMedias(string $cacheKey, array $data): void
    {
        Redis::setex(
            $cacheKey,
            10 * 60,
            json_encode($data)
        );
    }

    private function uploadIcon(SocialMedia $socialMedia, ?UploadedFile $file): void
    {
        if (!$file) {
            return;
        }

        $this->fileService
            ->attachFile([
                'file' => $file,
                'model' => $socialMedia,
                'modelName' => Str::of(SocialMedia::class)
                    ->classBasename()
                    ->lower()
                    ->value(),
                'subfolder' => 'icon',
                'mediaTagName' => 'icon',
                'cacheKey' => 'social_medias',
            ]);
    }

    private function deleteIcon(SocialMedia $socialMedia): void
    {
        $this->fileService
            ->deleteAttachedFile([
                'model' => $socialMedia,
                'mediaTagName' => 'icon',
            ]);
    }

    private function clearSocialMediasCache(): void
    {
        $this->clearCache('social_medias:*');
    }
}
