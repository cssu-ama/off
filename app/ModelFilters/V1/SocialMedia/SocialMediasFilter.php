<?php

namespace App\ModelFilters\V1\SocialMedia;

use App\Traits\V1\Core\Sanitization\NormalizesSearchContent;
use Illuminate\Database\Eloquent\Builder;

trait SocialMediasFilter
{
    use NormalizesSearchContent;

    public function filterCustomsearch(Builder $builder, $value): Builder
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value));

        return $builder->where(function ($query) use ($value) {
            $search = "%{$value}%";

            $query
                ->where('social_medias.title', 'ILIKE', $search);
        });
    }

    public function filterCustomsort(Builder $builder, $value): Builder
    {
        $sorts = [
            'created_at_desc' => ['social_medias.id', 'desc'],
        ];

        if (! isset($sorts[$value])) {
            return $builder;
        }

        [$column, $direction] = $sorts[$value];

        return $builder->orderBy($column, $direction);
    }
}
