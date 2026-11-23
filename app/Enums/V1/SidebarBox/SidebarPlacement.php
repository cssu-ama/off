<?php

namespace App\Enums\V1\SidebarBox;

enum SidebarPlacement: string
{
    case NONE = 'none';
    case SINGLE_POST = 'single_post';
    case HOME = 'home';
}
