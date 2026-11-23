<?php

namespace App\Services\V1\Post;

use App\Enums\V1\Post\PostStatus;
use App\Enums\V1\Role\UserRole;
use App\Http\Resources\V1\Post\Admin\PostCollection;
use App\Http\Resources\V1\Post\Admin\PostListCollection;
use App\Http\Resources\V1\Post\Public\PublicPostCollection;
use App\Http\Resources\V1\Post\Public\PublicPostListCollection;
use App\Models\V1\Post\Post;
use App\Services\V1\Core\Media\Interfaces\MediaServiceInterface;
use App\Services\V1\Post\Interfaces\PostServiceInterface;
use App\Traits\V1\Core\Cache\ClearsRedisCache;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

/**
 * Class PostService.
 */
class PostService implements PostServiceInterface
{
    use ClearsRedisCache;

    public function __construct(protected MediaServiceInterface $fileService)
    {
    }

    public function index(bool $hasPaginate): array
    {
        $cacheKey = $this->getPostsCacheKey('admin');

        if ($cached = $this->getPostsFromCache($cacheKey)) {
            return $cached;
        }

        $posts = $this->getPosts($hasPaginate);

        $this->cachePosts($cacheKey, $posts);

        return $posts;
    }

    public function publicIndex(bool $hasPaginate): array
    {
        $cacheKey = $this->getPostsCacheKey('public');

        if ($cached = $this->getPostsFromCache($cacheKey)) {
            return $cached;
        }

        $posts = $this->getPublicPosts($hasPaginate);

        $this->cachePosts($cacheKey, $posts);

        return $posts;
    }

    public function show(Post $post): Post|Model
    {
        $request = request();

        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? 'unknown-agent';
        $acceptLanguage = $request->header('Accept-Language', 'unknown-lang');

        $fingerprint = hash('sha256', "{$ip}|{$userAgent}|{$acceptLanguage}");

        $cacheKey = "views:{$post->id}:visitor:{$fingerprint}";

        $isNewView = (bool)Redis::set($cacheKey, 1, 'EX', 43200, 'NX');

        if ($isNewView) {
            $post->increment('views');
            $this->clearPostsCache();
        }

        return $post->refresh();
    }

    public function find(int $id): Post|Model
    {
        return Post::query()
            ->findOrFail($id);
    }

    public function store(array $inputs): Post|Model
    {
        return DB::transaction(function () use ($inputs) {
            $postAttributes = Arr::only(
                $inputs,
                new Post()->getFillable()
            );

            if (!array_key_exists('order', $postAttributes) || $postAttributes['order'] === null) {
                $postAttributes['order'] = (
                    (int)Post::query()->max('order')
                    ) + 1;
            } else {
                Post::query()
                    ->where('order', '>=', $postAttributes['order'])
                    ->increment('order');
            }

            $post = Post::query()->create($postAttributes);

            if (!$post) {
                throw new Exception('مشکل در ایجاد پست جدید');
            }

            $this->syncCategories(
                $post,
                $inputs['category_ids'] ?? []
            );

            $this->syncTags(
                $post,
                $inputs['tag_ids'] ?? []
            );

            $this->uploadThumbnail(
                $post,
                $inputs['thumbnail'] ?? null
            );

            $this->clearPostsCache();

            return $post;
        });
    }

    public function update(array $inputs, Post $post): Post|Model
    {
        $updatedPost = DB::transaction(function () use ($inputs, $post) {
            $post = Post::query()
                ->lockForUpdate()
                ->findOrFail($post->id);

            $postAttributes = Arr::only(
                $inputs,
                new Post()->getFillable()
            );

            if (array_key_exists('order', $postAttributes)) {
                $oldOrder = (int)$post->order;
                $newOrder = (int)$postAttributes['order'];

                if ($newOrder < $oldOrder) {
                    Post::query()
                        ->whereBetween('order', [$newOrder, $oldOrder - 1])
                        ->increment('order');

                } elseif ($newOrder > $oldOrder) {
                    Post::query()
                        ->where('order', '>', $oldOrder)
                        ->where('order', '<=', $newOrder)
                        ->decrement('order');
                }
            }

            if (!$post->update($postAttributes)) {
                throw new Exception('مشکل در بروزرسانی پست');
            }

            $this->syncCategories(
                $post,
                $inputs['category_ids'] ?? []
            );

            $this->syncTags(
                $post,
                $inputs['tag_ids'] ?? []
            );

            $this->uploadThumbnail(
                $post,
                $inputs['thumbnail'] ?? null
            );

            return $post->refresh();
        });

        $this->clearPostsCache();

        return $updatedPost;
    }

    public function indexStatus(): array
    {
        return [
            PostStatus::PUBLISHED,
            PostStatus::DRAFT,
        ];
    }

    public function changeStatus(array $inputs, Post $post): Post|Model
    {
        $post->update([
            'status' => $inputs['status']
        ]);

        $this->clearPostsCache();

        return $post;
    }

    public function changeOrder(array $inputs): void
    {
        DB::transaction(function () use ($inputs) {
            $orders = collect($inputs['orders']);

            $ids = $orders->pluck('id')->all();

            $posts = Post::query()
                ->whereIn('id', $ids)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($posts->count() !== count($ids)) {
                throw new Exception('یک یا چند پست موردنظر یافت نشد.');
            }

            foreach ($orders as $item) {
                $post = $posts->get($item['id']);

                $post->order = $item['order'];

                if (!$post->save()) {
                    throw new Exception('تغییر ترتیب پست‌ها با مشکل مواجه شد.');
                }
            }
        }, 3);

        $this->clearPostsCache();
    }

    public function destroy(Post $post): Post|Model
    {
        $this->deleteThumbnail($post);

        if (!$post->delete()) {
            throw new Exception('مشکل در حذف پست');
        }

        $this->clearPostsCache();

        return $post;
    }

    private function getPublicPosts(bool $hasPaginate): array
    {
        $query = Post::query()
            ->where(
                'posts.status',
                '=',
                PostStatus::PUBLISHED
            )
            ->ignoreRequest(['per_page', 'paginate'])
            ->filter();

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            return PublicPostCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return PublicPostListCollection::make(
            $query->get()
        )->resolve();
    }

    private function getPosts(bool $hasPaginate): array
    {
        $query = Post::query()
            ->when(
                !Auth::user()->hasAnyRole([
                    UserRole::ADMIN,
                    UserRole::MANAGER,
                ]),
                fn($query) => $query->where(
                    'posts.user_id',
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

            return PostCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return PostListCollection::make(
            $query->get()
        )->resolve();
    }

    private function getPostsCacheKey(string $cacheContext): string
    {
        $queryParams = request()->query();
        ksort($queryParams);

        return "posts:{$cacheContext}:" . md5(
                request()->path() .
                serialize($queryParams)
            );
    }

    private function getPostsFromCache(string $cacheKey): ?array
    {
        $data = Redis::get($cacheKey);

        return $data
            ? json_decode($data, true)
            : null;
    }

    private function cachePosts(string $cacheKey, array $data): void
    {
        Redis::setex(
            $cacheKey,
            10 * 60,
            json_encode($data)
        );
    }

    private function uploadThumbnail(Post $post, ?UploadedFile $file): void
    {
        if (!$file) {
            return;
        }

        $this->fileService
            ->attachFile([
                'file' => $file,
                'model' => $post,
                'modelName' => Str::of(Post::class)
                    ->classBasename()
                    ->lower()
                    ->value(),
                'subfolder' => 'thumbnail',
                'mediaTagName' => 'thumbnail',
                'cacheKey' => 'posts',
            ]);
    }

    private function syncCategories(Post $post, array $categoryIDs): void
    {
        if (empty($categoryIDs)) {
            return;
        }

        $post->categories()->sync($categoryIDs);
    }

    private function syncTags(Post $post, array $tagIDs): void
    {
        if (empty($tagIDs)) {
            return;
        }

        $post->tags()->sync($tagIDs);
    }

    private function deleteThumbnail(Post $post): void
    {
        $this->fileService
            ->deleteAttachedFile([
                'model' => $post,
                'mediaTagName' => 'thumbnail',
            ]);
    }

    private function clearPostsCache(): void
    {
        $this->clearCache('users:*');
        $this->clearCache('medias:*');
        $this->clearCache('comments:*');
        $this->clearCache('posts:*');
        $this->clearCache('categories:*');
        $this->clearCache('tags:*');
    }
}
