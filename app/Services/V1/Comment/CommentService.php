<?php

namespace App\Services\V1\Comment;

use App\Enums\V1\Comment\CommentStatus;
use App\Enums\V1\Role\UserRole;
use App\Http\Resources\V1\Comment\Admin\CommentCollection;
use App\Http\Resources\V1\Comment\Admin\CommentListCollection;
use App\Http\Resources\V1\Comment\Public\PublicCommentCollection;
use App\Http\Resources\V1\Comment\Public\PublicCommentListCollection;
use App\Models\V1\Comment\Comment;
use App\Services\V1\Comment\Interfaces\CommentServiceInterface;
use App\Services\V1\Post\Interfaces\PostServiceInterface;
use App\Traits\V1\Core\Cache\ClearsRedisCache;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

/**
 * Class CommentService.
 */
class CommentService implements CommentServiceInterface
{
    use ClearsRedisCache;

    public function __construct(protected PostServiceInterface $postService)
    {
    }

    public function index(bool $hasPaginate): array
    {
        $cacheKey = $this->getCommentsCacheKey('admin');

        if ($cached = $this->getCommentsFromCache($cacheKey)) {
            return $cached;
        }

        $comments = $this->getComments($hasPaginate);

        $this->cacheComments($cacheKey, $comments);

        return $comments;
    }

    public function publicIndex(bool $hasPaginate): array
    {
        $cacheKey = $this->getCommentsCacheKey('public');

        if ($cached = $this->getCommentsFromCache($cacheKey)) {
            return $cached;
        }

        $comments = $this->getPublicComments($hasPaginate);

        $this->cacheComments($cacheKey, $comments);

        return $comments;
    }

    public function store(array $inputs): Comment|Model
    {
        $post = $this->postService
            ->find($inputs['post_id']);

        $user = Auth::guard('api')->user();

        $isAdminOrManager = $user && $user->hasAnyRole([
                UserRole::MANAGER,
                UserRole::ADMIN,
            ]);

        $comment = $post->comments()->create([
            'comment' => $inputs['comment'],
            'parent_id' => $inputs['parent_id'] ?? null,

            'author_name' => $isAdminOrManager
                ? 'ادمین'
                : ($inputs['author_name'] ?? null),

            'phone' => $inputs['phone'] ?? null,
            'email' => $inputs['email'] ?? null,
            'title' => $inputs['title'] ?? null,

            'status' => $isAdminOrManager
                ? CommentStatus::PUBLISHED
                : CommentStatus::PENDING,
        ]);

        $this->clearCommentsCache();

        return $comment;
    }

    public function destroy(Comment $comment): Comment|Model
    {
        if (!$comment->delete()) {
            throw new Exception('مشکل در حذف نظر');
        }

        $this->clearCommentsCache();

        return $comment;
    }

    public function indexStatus(): array
    {
        return [
            CommentStatus::PUBLISHED,
            CommentStatus::PENDING,
            CommentStatus::REJECTED
        ];
    }

    public function changeStatus(array $inputs, Comment $comment): Comment|Model
    {
        $comment->update([
            'status' => $inputs['status']
        ]);

        $this->clearCommentsCache();

        return $comment;
    }

    private function getComments(bool $hasPaginate): mixed
    {
        $query = Comment::query()
            ->ignoreRequest(['per_page', 'paginate'])
            ->filter();

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            return CommentCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return CommentListCollection::make(
            $query->get()
        )->resolve();
    }

    private function getPublicComments(bool $hasPaginate): mixed
    {
        $query = Comment::query()
            ->where('comments.status', '=', CommentStatus::PUBLISHED)
            ->whereNull('parent_id')
            ->ignoreRequest(['per_page', 'paginate'])
            ->filter()
            ->with('publishedChildren');

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            return PublicCommentCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return PublicCommentListCollection::make(
            $query->get()
        )->resolve();
    }

    private function getCommentsCacheKey(string $cacheContext): string
    {
        $queryParams = request()->query();
        ksort($queryParams);

        return "comments:{$cacheContext}:" . md5(
                request()->path() .
                serialize($queryParams)
            );
    }

    private function getCommentsFromCache(string $cacheKey): ?array
    {
        $data = Redis::get($cacheKey);

        return $data
            ? json_decode($data, true)
            : null;
    }

    private function cacheComments(string $cacheKey, array $data): void
    {
        Redis::setex(
            $cacheKey,
            10 * 60,
            json_encode($data)
        );
    }

    private function clearCommentsCache(): void
    {
        $this->clearCache('users:*');
        $this->clearCache('medias:*');
        $this->clearCache('comments:*');
        $this->clearCache('posts:*');
        $this->clearCache('categories:*');
        $this->clearCache('tags:*');
    }
}
