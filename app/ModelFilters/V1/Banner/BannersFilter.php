<?php

namespace App\ModelFilters\V1\Banner;

use App\Traits\V1\Core\Sanitization\NormalizesSearchContent;
use Illuminate\Database\Eloquent\Builder;

trait BannersFilter
{
    use NormalizesSearchContent;

    public function filterCustomplacement(Builder $builder, $value): Builder
    {
        $placements = collect(explode(',', (string)$value))
            ->map(fn($placement) => trim($placement))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($placements)) {
            return $builder;
        }

        return $builder->whereIn('banners.placement', $placements);
    }

    public function filterCustomsort(Builder $builder, $value): Builder
    {
        $sorts = [
            'created_at_desc' => ['banners.id', 'desc'],
        ];

        if (!isset($sorts[$value])) {
            return $builder;
        }

        [$column, $direction] = $sorts[$value];

        return $builder->orderBy($column, $direction);
    }
}
