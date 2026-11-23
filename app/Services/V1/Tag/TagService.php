<?php

namespace App\Services\V1\Tag;

use App\Enums\V1\Post\PostStatus;
use App\Http\Resources\V1\Tag\Admin\TagCollection;
use App\Http\Resources\V1\Tag\Admin\TagListCollection;
use App\Http\Resources\V1\Tag\Public\PublicTagCollection;
use App\Http\Resources\V1\Tag\Public\PublicTagListCollection;
use App\Models\V1\Tag\Tag;
use App\Services\V1\Tag\Interfaces\TagServiceInterface;
use App\Traits\V1\Core\Cache\ClearsRedisCache;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

/**
 * Class TagService.
 */
class TagService implements TagServiceInterface
{
    use ClearsRedisCache;

    public function index(bool $hasPaginate): array
    {
        $cacheKey = $this->getTagsCacheKey('admin');

        if ($cached = $this->getTagsFromCache($cacheKey)) {
            return $cached;
        }

        $tags = $this->getTags($hasPaginate);

        $this->cacheTags($cacheKey, $tags);

        return $tags;
    }

    public function publicIndex(bool $hasPaginate): array
    {
        $cacheKey = $this->getTagsCacheKey('public');

        if ($cached = $this->getTagsFromCache($cacheKey)) {
            return $cached;
        }

        $tags = $this->getPublicTags($hasPaginate);

        $this->cacheTags($cacheKey, $tags);

        return $tags;
    }

    public function store(array $inputs): Tag|Model
    {
        $tag = Tag::query()
            ->create($inputs);

        if (!$tag) {
            throw new Exception('مشکل در ایجاد تگ جدید');
        }

        $this->clearTagsCache();

        return $tag;
    }

    public function update(array $inputs, Tag $tag): Tag|Model
    {
        if (!$tag->update($inputs)) {
            throw new Exception('مشکل در بروزرسانی تگ');
        }

        $this->clearTagsCache();

        return $tag;
    }

    public function destroy(Tag $tag): Tag|Model
    {
        if (!$tag->delete()) {
            throw new Exception('مشکل در حذف تگ');
        }

        $this->clearTagsCache();

        return $tag;
    }

    private function getTags(bool $hasPaginate): array
    {
        $query = Tag::query()
            ->ignoreRequest(['per_page', 'paginate'])
            ->filter();

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            return TagCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return TagListCollection::make(
            $query->get()
        )->resolve();
    }

    private function getPublicTags(bool $hasPaginate): array
    {
        $query = Tag::query()
            ->whereHas('posts', function ($query) {
                $query->where(
                    'posts.status',
                    '=',
                    PostStatus::PUBLISHED
                );
            })
            ->ignoreRequest(['per_page', 'paginate'])
            ->filter();

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            return PublicTagCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return PublicTagListCollection::make(
            $query->get()
        )->resolve();
    }

    private function getTagsCacheKey(string $cacheContext): string
    {
        $queryParams = request()->query();
        ksort($queryParams);

        return "tags:{$cacheContext}:" . md5(
                request()->path() .
                serialize($queryParams)
            );
    }

    private function getTagsFromCache(string $cacheKey): ?array
    {
        $data = Redis::get($cacheKey);

        return $data
            ? json_decode($data, true)
            : null;
    }

    private function cacheTags(string $cacheKey, array $data): void
    {
        Redis::setex(
            $cacheKey,
            10 * 60,
            json_encode($data)
        );
    }

    private function clearTagsCache(): void
    {
        $this->clearCache('users:*');
        $this->clearCache('medias:*');
        $this->clearCache('comments:*');
        $this->clearCache('posts:*');
        $this->clearCache('categories:*');
        $this->clearCache('tags:*');
    }
}
