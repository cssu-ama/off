<?php

namespace App\Enums\V1\Banner;

enum LinkRelation: string
{
    case NOFOLLOW = 'nofollow';
    case FOLLOW = 'follow';
    case SPONSORED = 'sponsored';
    case UGC = 'ugc';
}
