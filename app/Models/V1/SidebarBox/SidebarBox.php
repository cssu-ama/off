<?php

namespace App\Models\V1\SidebarBox;

use App\Enums\V1\Post\PostStatus;
use App\Enums\V1\SidebarBox\PostSelectionType;
use App\Enums\V1\SidebarBox\SidebarPlacement;
use App\ModelFilters\V1\SidebarBox\SidebarBoxesFilter;
use App\Models\V1\Category\Category;
use App\Models\V1\Post\Post;
use App\Models\V1\Tag\Tag;
use App\Models\V1\User\User;
use App\Policies\V1\SidebarBox\SidebarBoxPolicy;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

#[UsePolicy(SidebarBoxPolicy::class)]
class SidebarBox extends Model
{
    use Filterable, SidebarBoxesFilter;

    protected $fillable = [
        'title',
        'sort_order',
        'placement',
        'post_selection_type',
        'category_id',
        'tag_id',
        'user_id',
        'posts_limit',
    ];

    private static array $whiteListFilter = [
        'category_id',
        'tag_id',
        'placement'
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'posts_limit' => 'integer',
            'placement' => SidebarPlacement::class,
            'post_selection_type' => PostSelectionType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(
            Post::class,
            'sidebar_box_post',
            'sidebar_box_id',
            'post_id'
        )
            ->using(SidebarBoxPost::class)
            ->withPivot([
                'id',
                'post_order',
            ])
            ->withTimestamps()
            ->orderByPivot('post_order');
    }

    public function resolvePosts(): Collection
    {
        return match ($this->post_selection_type) {
            PostSelectionType::MANUAL => $this->posts()
                ->where('posts.status', PostStatus::PUBLISHED)
                ->orderByPivot('post_order')
                ->limit($this->posts_limit)
                ->get(),

            PostSelectionType::MOST_VIEWED => $this->automaticPostsQuery()
                ->orderByDesc('posts.views')
                ->limit($this->posts_limit)
                ->get(),

            PostSelectionType::LATEST => $this->automaticPostsQuery()
                ->orderByDesc('posts.id')
                ->limit($this->posts_limit)
                ->get(),

            PostSelectionType::NONE => new Collection(),
        };
    }

    private function automaticPostsQuery(): Builder
    {
        $query = Post::query()
            ->where('posts.status', PostStatus::PUBLISHED);

        if ($this->category_id) {
            $query->whereHas('categories', function (Builder $query) {
                $query->where('categories.id', $this->category_id);
            });
        }

        if ($this->tag_id) {
            $query->whereHas('tags', function (Builder $query) {
                $query->where('tags.id', $this->tag_id);
            });
        }

        return $query;
    }
}
