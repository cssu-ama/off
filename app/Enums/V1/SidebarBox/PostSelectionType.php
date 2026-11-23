<?php

namespace App\Enums\V1\SidebarBox;

enum PostSelectionType: string
{
    case NONE = 'none';
    case MANUAL = 'manual';
    case MOST_VIEWED = 'most_viewed';
    case LATEST = 'latest';
}
