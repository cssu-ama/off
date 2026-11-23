<?php

namespace App\Enums\V1\Comment;

enum CommentStatus: string
{
    case PENDING = 'pending';
    case REJECTED = 'rejected';
    case PUBLISHED = 'published';
}
