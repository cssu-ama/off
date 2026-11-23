<?php

namespace App\ModelFilters\V1\Post;

use App\Traits\V1\Core\Sanitization\NormalizesSearchContent;
use Illuminate\Database\Eloquent\Builder;

trait PostsFilter
{
    use NormalizesSearchContent;

    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function filterCustomsearch(Builder $builder, $value): Builder
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value));

        return $builder->where(function ($query) use ($value) {
            $search = "%{$value}%";

            $query
                ->where('posts.title', 'ILIKE', $search)
                ->orWhereRaw(
                    $this->normalizedColumn('posts.description') . ' ILIKE ?',
                    [$search]
                )
                ->orWhereRaw(
                    $this->normalizedColumn('posts.content') . ' ILIKE ?',
                    [$search]
                );
        });
    }

    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function filterCustomcategory_id(Builder $builder, $value): Builder
    {
        return $builder->whereHas('categories', function ($query) use ($value) {
            $query->where('categories.id', $value);
        });
    }

    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function filterCustomtag_id(Builder $builder, $value): Builder
    {
        return $builder->whereHas('tags', function ($query) use ($value) {
            $query->where('tags.id', $value);
        });
    }

    /**
     * @param Builder $builder
     * @param mixed $value
     * @return Builder
     */
    public function filterCustomuser_name(Builder $builder, $value): Builder
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value));
        $search = "%{$value}%";

        return $builder->whereHas('author', function ($query) use ($search) {
            $query->where('user_name', 'ILIKE', $search)
                ->orWhere('name', 'ILIKE', $search);
        });
    }

    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function filterCustomexcept_from(Builder $builder, $value): Builder
    {
        return $builder
            ->where('posts.id', '!=', $value);
    }

    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function filterCustomsort(Builder $builder, $value): Builder
    {
        $sorts = [
            'created_at_desc' => ['posts.id', 'desc'],
            'views_desc' => ['posts.views', 'desc'],
            'order_desc' => ['posts.order', 'desc'],
            'order_asc' => ['posts.order', 'asc'],
        ];

        if (!isset($sorts[$value])) {
            return $builder;
        }

        [$column, $direction] = $sorts[$value];

        return $builder
            ->orderBy($column, $direction);
    }
}
