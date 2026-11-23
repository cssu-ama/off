<?php

namespace App\Models\V1\Comment;

use App\Enums\V1\Comment\CommentStatus;
use App\ModelFilters\V1\Comment\CommentsFilter;
use App\Models\V1\Post\Post;
use App\Models\V1\User\User;
use App\Policies\V1\Comment\CommentPolicy;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[UsePolicy(CommentPolicy::class)]
class Comment extends Model
{
    use Filterable, CommentsFilter;

    protected $fillable = [
        'comment',
        'commentable_id',
        'commentable_type',
        'parent_id',
        'status',
        'author_name',
        'phone',
        'email',
        'title',
    ];

    private static array $whiteListFilter = [
        'status'
    ];

    protected $casts = [
        'status' => CommentStatus::class,
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(
            Post::class,
            'commentable_id'
        )->where('commentable_type', Post::class);
    }

    public function publishedParent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id')
            ->where('status', CommentStatus::PUBLISHED);
    }

    public function publishedChildren()
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->where('status', CommentStatus::PUBLISHED)
            ->with('publishedChildren');
    }
}
