<?php

namespace App\ModelFilters\V1\Comment;

use Illuminate\Database\Eloquent\Builder;

trait CommentsFilter
{
    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function filterCustompost_id(Builder $builder, $value): Builder
    {
        return $builder->whereHas('post', function ($query) use ($value) {
            $query->where('posts.id', $value);
        });
    }

    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function filterCustomsort(Builder $builder, $value): Builder
    {
        $sorts = [
            'created_at_desc' => ['comments.id', 'desc'],
        ];

        if (!isset($sorts[$value])) {
            return $builder;
        }

        [$column, $direction] = $sorts[$value];

        return $builder
            ->orderBy($column, $direction);
    }
}
