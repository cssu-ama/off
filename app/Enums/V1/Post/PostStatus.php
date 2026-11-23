<?php

namespace App\Enums\V1\Post;

enum PostStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}
