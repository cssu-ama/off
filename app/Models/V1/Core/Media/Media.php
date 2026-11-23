<?php

namespace App\Models\V1\Core\Media;

use App\ModelFilters\V1\Core\Media\MediasFilter;
use App\Policies\V1\Core\Media\MediaPolicy;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Plank\Mediable\Media as BaseMedia;

#[UsePolicy(MediaPolicy::class)]
class Media extends BaseMedia
{
    use Filterable, MediasFilter;

    private static array $whiteListFilter = [];
}
