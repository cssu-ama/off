<?php

namespace App\ModelFilters\V1\SidebarBox;

use App\Traits\V1\Core\Sanitization\NormalizesSearchContent;
use Illuminate\Database\Eloquent\Builder;

trait SidebarBoxesFilter
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
                ->where('sidebar_boxes.title', 'ILIKE', $search);
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
            'created_at_desc' => ['sidebar_boxes.id', 'desc'],
            'order_desc' => ['sidebar_boxes.sort_order', 'desc'],
            'order_asc' => ['sidebar_boxes.sort_order', 'asc'],
        ];

        if (!isset($sorts[$value])) {
            return $builder;
        }

        [$column, $direction] = $sorts[$value];

        return $builder
            ->orderBy($column, $direction);
    }
}
