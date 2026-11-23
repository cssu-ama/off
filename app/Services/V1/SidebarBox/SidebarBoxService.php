<?php

namespace App\Services\V1\SidebarBox;

use App\Enums\V1\Post\PostStatus;
use App\Enums\V1\SidebarBox\PostSelectionType;
use App\Enums\V1\SidebarBox\SidebarPlacement;
use App\Http\Resources\V1\SidebarBox\Admin\SidebarBoxCollection;
use App\Http\Resources\V1\SidebarBox\Admin\SidebarBoxListCollection;
use App\Http\Resources\V1\SidebarBox\Public\PublicSidebarBoxCollection;
use App\Http\Resources\V1\SidebarBox\Public\PublicSidebarBoxListCollection;
use App\Models\V1\Post\Post;
use App\Models\V1\SidebarBox\SidebarBox;
use App\Models\V1\SidebarBox\SidebarBoxPost;
use App\Services\V1\SidebarBox\Interfaces\SidebarBoxServiceInterface;
use App\Traits\V1\Core\Cache\ClearsRedisCache;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Throwable;

/**
 * Class SidebarBoxService.
 */
class SidebarBoxService implements SidebarBoxServiceInterface
{
    use ClearsRedisCache;

    public function index(bool $hasPaginate): array
    {
        $cacheKey = $this->getSidebarBoxesCacheKey('admin');

        if ($cached = $this->getSidebarBoxesFromCache($cacheKey)) {
            return $cached;
        }

        $sidebarBoxes = $this->getSidebarBoxes($hasPaginate);

        $this->cacheSidebarBoxes($cacheKey, $sidebarBoxes);

        return $sidebarBoxes;
    }

    public function publicIndex(bool $hasPaginate): array
    {
        $cacheKey = $this->getSidebarBoxesCacheKey('public');

        if ($cached = $this->getSidebarBoxesFromCache($cacheKey)) {
            return $cached;
        }

        $posts = $this->getPublicSidebarBoxes($hasPaginate);

        $this->cacheSidebarBoxes($cacheKey, $posts);

        return $posts;
    }

    public function indexPlacement(): array
    {
        return [
            SidebarPlacement::NONE,
            SidebarPlacement::HOME,
            SidebarPlacement::SINGLE_POST,
        ];
    }

    public function indexPostSelectionType(): array
    {
        return [
            PostSelectionType::NONE,
            PostSelectionType::LATEST,
            PostSelectionType::MANUAL,
            PostSelectionType::MOST_VIEWED,
        ];
    }

    public function store(array $inputs): SidebarBox|Model
    {
        return DB::transaction(function () use ($inputs) {
            $sidebarBoxAttributes = Arr::only(
                $inputs,
                new SidebarBox()->getFillable()
            );

            if (!array_key_exists('sort_order', $sidebarBoxAttributes) || $sidebarBoxAttributes['sort_order'] === null) {
                $sidebarBoxAttributes['sort_order'] = (
                    (int)SidebarBox::query()->max('sort_order')
                    ) + 1;

            } else {
                SidebarBox::query()
                    ->where('sort_order', '>=', $sidebarBoxAttributes['sort_order'])
                    ->increment('sort_order');
            }

            $sidebarBox = SidebarBox::query()->create(
                $sidebarBoxAttributes
            );

            if (!$sidebarBox) {
                throw new Exception('مشکل در ایجاد مورد جدید.');
            }

            if ($sidebarBox->post_selection_type === PostSelectionType::MANUAL) {
                $this->syncManualPosts(
                    $sidebarBox,
                    $inputs['post_ids'] ?? []
                );
            }

            $this->clearSidebarBoxesCache();

            return $sidebarBox;
        });
    }

    public function update(array $inputs, SidebarBox $sidebarBox): SidebarBox|Model
    {
        $updatedSidebarBox = DB::transaction(function () use ($inputs, $sidebarBox) {
            $sidebarBox = SidebarBox::query()
                ->lockForUpdate()
                ->findOrFail($sidebarBox->id);

            $sidebarBoxAttributes = Arr::only(
                $inputs,
                new SidebarBox()->getFillable()
            );

            if (array_key_exists('sort_order', $sidebarBoxAttributes)) {
                $oldOrder = (int)$sidebarBox->sort_order;
                $newOrder = (int)$sidebarBoxAttributes['sort_order'];

                if ($newOrder < $oldOrder) {
                    SidebarBox::query()
                        ->whereBetween('sort_order', [$newOrder, $oldOrder - 1])
                        ->increment('sort_order');

                } elseif ($newOrder > $oldOrder) {
                    SidebarBox::query()
                        ->where('sort_order', '>', $oldOrder)
                        ->where('sort_order', '<=', $newOrder)
                        ->decrement('sort_order');
                }
            }

            if (!$sidebarBox->update($sidebarBoxAttributes)) {
                throw new Exception('مشکل در بروزرسانی باکس سایدبار');
            }

            if ($sidebarBox->post_selection_type === PostSelectionType::MANUAL) {
                $this->syncManualPosts(
                    $sidebarBox,
                    $inputs['post_ids'] ?? []
                );
            }

            return $sidebarBox->refresh();
        });

        $this->clearSidebarBoxesCache();

        return $updatedSidebarBox;
    }

    public function changeOrder(array $inputs): void
    {
        DB::transaction(function () use ($inputs) {
            $orders = collect($inputs['orders']);

            $ids = $orders->pluck('id')->unique()->values()->all();

            $sidebarBoxes = SidebarBox::query()
                ->whereIn('id', $ids)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($sidebarBoxes->count() !== count($ids)) {
                throw new Exception('یک یا چند باکس موردنظر یافت نشد.');
            }

            foreach ($orders as $item) {
                $sidebarBox = $sidebarBoxes->get($item['id']);

                $sidebarBox->sort_order = $item['order'];

                if (!$sidebarBox->save()) {
                    throw new Exception('تغییر ترتیب باکس‌ها با مشکل مواجه شد.');
                }
            }
        }, 3);

        $this->clearSidebarBoxesCache();
    }

    public function changePostOrder(array $inputs, SidebarBox $sidebarBox): void
    {
        DB::transaction(function () use ($inputs, $sidebarBox) {
            $orders = collect($inputs['orders']);

            $ids = $orders->pluck('id')->all();

            $sidebarBoxPosts = SidebarBoxPost::query()
                ->where('sidebar_box_id', $sidebarBox->id)
                ->whereIn('post_id', $ids)
                ->orderBy('post_id')
                ->lockForUpdate()
                ->get()
                ->keyBy('post_id');

            if ($sidebarBoxPosts->count() !== count($ids)) {
                throw new Exception('یک یا چند نوشته باکس موردنظر یافت نشد.');
            }

            foreach ($orders as $item) {
                $sidebarBoxPost = $sidebarBoxPosts->get($item['id']);

                $sidebarBoxPost->post_order = $item['order'];

                if (!$sidebarBoxPost->save()) {
                    throw new Exception('تغییر ترتیب نوشته های باکس با مشکل مواجه شد.');
                }
            }
        }, 3);

        $this->clearSidebarBoxesCache();
    }

    public function destroy(SidebarBox $sidebarBox): SidebarBox|Model
    {
        if (!$sidebarBox->delete()) {
            throw new Exception('مشکل در حذف باکس');
        }

        $this->clearSidebarBoxesCache();

        return $sidebarBox;
    }

    private function getSidebarBoxesCacheKey(string $cacheContext): string
    {
        $queryParams = request()->query();
        ksort($queryParams);

        return "sidebar_boxes:{$cacheContext}:" . md5(
                request()->path() .
                serialize($queryParams)
            );
    }

    private function getSidebarBoxesFromCache(string $cacheKey): ?array
    {
        $data = Redis::get($cacheKey);

        return $data
            ? json_decode($data, true)
            : null;
    }

    private function getSidebarBoxes(bool $hasPaginate): array
    {
        $query = SidebarBox::query()
            ->ignoreRequest(['per_page', 'paginate'])
            ->filter();

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            $sidebarBoxes = $query->paginate($perPage, ['*'], 'page');

            $this->loadSidebarBoxPosts($sidebarBoxes->getCollection());

            return SidebarBoxCollection::make($sidebarBoxes)->resolve();
        }

        $sidebarBoxes = $query->get();

        $this->loadSidebarBoxPosts($sidebarBoxes);

        return SidebarBoxListCollection::make($sidebarBoxes)->resolve();
    }

    private function loadSidebarBoxPosts(Collection $sidebarBoxes): void
    {
        $sidebarBoxes->each(function (SidebarBox $sidebarBox) {
            $sidebarBox->setRelation(
                'posts',
                $sidebarBox->resolvePosts()
            );
        });
    }

    private function getPublicSidebarBoxes(bool $hasPaginate): array
    {
        $query = SidebarBox::query()
            ->where('sidebar_boxes.placement', '!=', SidebarPlacement::NONE)
            ->ignoreRequest(['per_page', 'paginate'])
            ->filter();

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            $sidebarBoxes = $query->paginate($perPage, ['*'], 'page');

            $this->loadSidebarBoxPosts($sidebarBoxes->getCollection());

            return PublicSidebarBoxCollection::make($sidebarBoxes)->resolve();
        }

        $sidebarBoxes = $query->get();

        $this->loadSidebarBoxPosts($sidebarBoxes);

        return PublicSidebarBoxListCollection::make($sidebarBoxes)->resolve();
    }

    private function cacheSidebarBoxes(string $cacheKey, array $data): void
    {
        Redis::setex(
            $cacheKey,
            10 * 60,
            json_encode($data)
        );
    }

    /**
     * @throws Exception
     */
    private function syncManualPosts(
        SidebarBox $sidebarBox,
        array      $postIDs
    ): void
    {
        $postIDs = array_values($postIDs);

        if (count($postIDs) > 20) {
            throw new Exception('حداکثر 20 مطلب قابل انتخاب است.');
        }

        if (count($postIDs) !== count(array_unique($postIDs))) {
            throw new Exception('انتخاب مطالب تکراری مجاز نیست.');
        }

        $publishedPostsCount = Post::query()
            ->whereIn('id', $postIDs)
            ->where('status', PostStatus::PUBLISHED)
            ->count();

        if ($publishedPostsCount !== count($postIDs)) {
            throw new Exception('فقط مطالب منتشرشده قابل انتخاب هستند.');
        }

        $syncData = [];

        foreach ($postIDs as $index => $postID) {
            $syncData[$postID] = [
                'post_order' => $index + 1,
            ];
        }

        $sidebarBox->posts()->sync($syncData);
    }

    private function clearSidebarBoxesCache(): void
    {
        $this->clearCache('sidebar_boxes:*');
    }
}
