<?php

namespace App\ModelFilters\V1\Role;

use Illuminate\Database\Eloquent\Builder;

trait RolesFilter
{
    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function filterCustomsort(Builder $builder, $value): Builder
    {
        $sorts = [
            'created_at_desc' => ['roles.id', 'desc'],
        ];

        if (!isset($sorts[$value])) {
            return $builder;
        }

        [$column, $direction] = $sorts[$value];

        return $builder
            ->orderBy($column, $direction);
    }
}
