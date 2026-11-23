<?php

namespace App\ModelFilters\V1\User;

use Illuminate\Database\Eloquent\Builder;

trait UsersFilter
{
    /**
     * This is a sample custom query
     * @param Builder $builder
     * @param $value
     *
     * @return Builder
     */
    public function filterCustomsearch(Builder $builder, $value)
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value));

        return $builder->where(function ($query) use ($value) {
            $search = "%{$value}%";

            $query
                ->where('users.name', 'ILIKE', $search)
                ->orWhere('users.user_name', 'ILIKE', $search)
                ->orWhere('users.phone', 'ILIKE', $search);
        });
    }

    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function filterCustomrole(Builder $builder, $value): Builder
    {
        return $builder->whereHas('roles', function ($query) use ($value) {
            $query->where('roles.name', $value);
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
            'created_at_desc' => ['users.id', 'desc'],
        ];

        if (!isset($sorts[$value])) {
            return $builder;
        }

        [$column, $direction] = $sorts[$value];

        return $builder
            ->orderBy($column, $direction);
    }
}
