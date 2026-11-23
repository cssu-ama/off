<?php

namespace App\ModelFilters\V1\Category;

use App\Enums\V1\Post\PostStatus;
use App\Traits\V1\Core\Sanitization\NormalizesSearchContent;
use Illuminate\Database\Eloquent\Builder;

trait CategoriesFilter
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
                ->where('categories.title', 'ILIKE', $search)
                ->orWhereRaw(
                    $this->normalizedColumn('categories.description') . ' ILIKE ?',
                    [$search]
                );;
        });
    }

    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function filterCustomsort(Builder $builder, $value): Builder
    {
        if ($value === 'most_viewed') {
            return $builder
                ->withSum([
                    'posts as total_post_views' => function ($query) {
                        $query->where('posts.status', PostStatus::PUBLISHED);
                    }
                ], 'views')
                ->orderByRaw('total_post_views DESC NULLS LAST');
        }

        $sorts = [
            'created_at_desc' => ['categories.id', 'desc'],
        ];

        if (!isset($sorts[$value])) {
            return $builder;
        }

        [$column, $direction] = $sorts[$value];

        return $builder
            ->orderBy($column, $direction);
    }
}
