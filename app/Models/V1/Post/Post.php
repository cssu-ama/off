<?php

namespace App\Models\V1\Post;

use App\Enums\V1\Post\PostStatus;
use App\ModelFilters\V1\Post\PostsFilter;
use App\Models\V1\Category\Category;
use App\Models\V1\Comment\Comment;
use App\Models\V1\SidebarBox\SidebarBox;
use App\Models\V1\SidebarBox\SidebarBoxPost;
use App\Models\V1\Tag\Tag;
use App\Models\V1\User\User;
use App\Policies\V1\Post\PostPolicy;
use App\Traits\V1\Core\Slug\HasUniqueSlug;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Plank\Mediable\Mediable;

#[UsePolicy(PostPolicy::class)]
class Post extends Model
{
    use Mediable, Filterable, PostsFilter, HasUniqueSlug;

    protected $fillable = [
        'title',
        'description',
        'content',
        'user_id',
        'status',
        'is_top_headline',
        'is_headline',
        'is_specific',
        'order'
    ];

    private static array $whiteListFilter = [
        'slug',
        'status',
        'is_top_headline',
        'is_headline',
        'is_specific',
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'is_top_headline' => 'boolean',
        'is_headline' => 'boolean',
        'is_specific' => 'boolean',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_post');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_post');
    }

    public function sidebarBoxes(): BelongsToMany
    {
        return $this->belongsToMany(
            SidebarBox::class,
            'sidebar_box_post',
            'post_id',
            'sidebar_box_id'
        )
            ->using(SidebarBoxPost::class)
            ->withPivot([
                'id',
                'post_order',
            ])
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::saved(function (Post $post): void {
            self::makeIsTopHeadlineColumnFalse($post);
        });
    }

    private static function makeIsTopHeadlineColumnFalse(Post $post): void
    {
        if (!$post->is_top_headline) {
            return;
        }

        static::query()
            ->where('is_top_headline', true)
            ->where('id', '!=', $post->id)
            ->update(['is_top_headline' => false]);
    }

    public function getReadingDurationAttribute(): int
    {
        $html = $this->content ?? '';

        $html = preg_replace(
            '~<(script|style)\b[^>]*>.*?</\1>~is',
            ' ',
            $html
        ) ?? $html;

        $html = preg_replace(
            '~<(br|/p|/div|/li|/h[1-6]|/blockquote|/section|/article)\b[^>]*>~i',
            ' ',
            $html
        ) ?? $html;

        $text = html_entity_decode(
            strip_tags($html),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $text = preg_replace('/[\p{Z}\s]+/u', ' ', trim($text)) ?? '';

        $wordCount = $text === ''
            ? 0
            : count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));

        return $wordCount === 0
            ? 0
            : (int)ceil($wordCount / 200);
    }
}
