<?php

namespace App\Services\V1\Category;

use App\Enums\V1\Post\PostStatus;
use App\Http\Resources\V1\Category\Admin\CategoryCollection;
use App\Http\Resources\V1\Category\Admin\CategoryListCollection;
use App\Http\Resources\V1\Category\Public\PublicCategoryCollection;
use App\Http\Resources\V1\Category\Public\PublicCategoryListCollection;
use App\Models\V1\Category\Category;
use App\Services\V1\Category\Interfaces\CategoryServiceInterface;
use App\Traits\V1\Core\Cache\ClearsRedisCache;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

/**
 * Class CategoryService.
 */
class CategoryService implements CategoryServiceInterface
{
    use ClearsRedisCache;

    public function index(bool $hasPaginate): array
    {
        $cacheKey = $this->getCategoriesCacheKey('admin');

        if ($cached = $this->getCategoriesFromCache($cacheKey)) {
            return $cached;
        }

        $categories = $this->getCategories($hasPaginate);

        $this->cacheCategories($cacheKey, $categories);

        return $categories;
    }

    public function publicIndex(bool $hasPaginate): array
    {
        $cacheKey = $this->getCategoriesCacheKey('public');

        if ($cached = $this->getCategoriesFromCache($cacheKey)) {
            return $cached;
        }

        $categories = $this->getPublicCategories($hasPaginate);

        $this->cacheCategories($cacheKey, $categories);

        return $categories;
    }

    public function find(int $id): Category|Model
    {
        return Category::query()
            ->findOrFail($id);
    }

    public function store(array $inputs): Category|Model
    {
        $category = Category::query()
            ->create($inputs);

        if (!$category) {
            throw new Exception('مشکل در ایجاد دسته بندی جدید');
        }

        $this->clearCategoriesCache();

        return $category;
    }

    public function update(array $inputs, Category $category): Category|Model
    {
        $this->isLoop(
            $inputs['parent_id'],
            $category
        );

        if (!$category->update($inputs)) {
            throw new Exception('مشکل در بروزرسانی دسته بندی');
        }

        $this->clearCategoriesCache();

        return $category;
    }

    public function destroy(Category $category): Category|Model
    {
        if (!$category->delete()) {
            throw new Exception('مشکل در حذف دسته بندی');
        }

        $this->clearCategoriesCache();

        return $category;
    }

    private function getCategories(bool $hasPaginate): array
    {
        $query = Category::query()
            ->ignoreRequest(['per_page', 'paginate'])
            ->filter();

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            return CategoryCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return CategoryListCollection::make(
            $query->get()
        )->resolve();
    }

    private function getPublicCategories(bool $hasPaginate): array
    {
        $query = Category::query()
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

            return PublicCategoryCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return PublicCategoryListCollection::make(
            $query->get()
        )->resolve();
    }

    private function getCategoriesCacheKey(string $cacheContext): string
    {
        $queryParams = request()->query();
        ksort($queryParams);

        return "categories:{$cacheContext}:" . md5(
                request()->path() .
                serialize($queryParams)
            );
    }

    private function getCategoriesFromCache(string $cacheKey): ?array
    {
        $data = Redis::get($cacheKey);

        return $data
            ? json_decode($data, true)
            : null;
    }

    private function cacheCategories(string $cacheKey, array $data): void
    {
        Redis::setex(
            $cacheKey,
            10 * 60,
            json_encode($data)
        );
    }

    private function isLoop(?int $parentID, Category $category): void
    {
        if (!$parentID) {
            return;
        }

        $parent = $this->find($parentID);

        while ($parent !== null) {
            if ($parent->id === $category->id) {
                throw new Exception(
                    'امکان انتخاب این والد وجود ندارد؛ زیرا باعث ایجاد حلقه در دسته‌بندی‌ها می‌شود.'
                );
            }

            $parent = $parent->parent;
        }
    }

    private function clearCategoriesCache(): void
    {
        $this->clearCache('users:*');
        $this->clearCache('medias:*');
        $this->clearCache('comments:*');
        $this->clearCache('posts:*');
        $this->clearCache('categories:*');
        $this->clearCache('tags:*');
    }
}
