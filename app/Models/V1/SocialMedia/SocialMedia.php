<?php

namespace App\Models\V1\SocialMedia;

use App\ModelFilters\V1\SocialMedia\SocialMediasFilter;
use App\Policies\V1\SocialMedia\SocialMediaPolicy;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

#[UsePolicy(SocialMediaPolicy::class)]
class SocialMedia extends Model
{
    use Filterable, SocialMediasFilter, Mediable;

    protected $table = 'social_medias';

    protected $fillable = [
        'title',
        'link',
        'user_id',
    ];

    private static array $whiteListFilter = [];
}
