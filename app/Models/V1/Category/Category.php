<?php

namespace App\Models\V1\Category;

use App\ModelFilters\V1\Category\CategoriesFilter;
use App\Models\V1\Post\Post;
use App\Policies\V1\Category\CategoryPolicy;
use App\Traits\V1\Core\Slug\HasUniqueSlug;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[UsePolicy(CategoryPolicy::class)]
class Category extends Model
{
    use Filterable, CategoriesFilter, HasUniqueSlug;

    protected $fillable = [
        'title',
        'description',
        'user_id',
        'parent_id',
    ];

    private static array $whiteListFilter = [
        'slug'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'category_post');
    }
}
