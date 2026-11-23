<?php

namespace App\Models\V1\Tag;

use App\ModelFilters\V1\Tag\TagsFilter;
use App\Models\V1\Post\Post;
use App\Policies\V1\Tag\TagPolicy;
use App\Traits\V1\Core\Slug\HasUniqueSlug;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[UsePolicy(TagPolicy::class)]
class Tag extends Model
{
    use Filterable, TagsFilter, HasUniqueSlug;

    protected $fillable = [
        'title',
        'description',
        'user_id',
    ];

    private static array $whiteListFilter = [
        'slug'
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'tag_post');
    }
}
