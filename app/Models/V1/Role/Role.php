<?php

namespace App\Models\V1\Role;

use App\Enums\V1\Role\UserRole;
use App\ModelFilters\V1\Role\RolesFilter;
use App\Policies\V1\Role\RolePolicy;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;

#[UsePolicy(RolePolicy::class)]
class Role extends Model
{
    use Filterable, RolesFilter;

    protected $fillable = [
        'name',
    ];

    private static array $whiteListFilter = [];

    protected $casts = [
        'status' => UserRole::class,
    ];
}
