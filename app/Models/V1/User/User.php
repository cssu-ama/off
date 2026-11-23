<?php

namespace App\Models\V1\User;

use App\ModelFilters\V1\User\UsersFilter;
use App\Policies\V1\User\UserPolicy;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Plank\Mediable\Mediable;
use Spatie\Permission\Traits\HasRoles;

#[UsePolicy(UserPolicy::class)]
class User extends Authenticatable implements JWTSubject
{
    use HasRoles, Filterable, UsersFilter, Mediable;

    protected string $guard_name = 'api';
    protected $fillable = [
        'name',
        'phone',
        'user_name'
    ];

    private static array $whiteListFilter = [
        'user_name'
    ];

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
