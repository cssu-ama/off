<?php

namespace App\Enums\V1\Banner;

enum BannerPlacement: string
{
    case NONE = 'none';
    case GLOBAL_TOPBAR = 'global_topbar';
    case HOME_HERO_TOP = 'home_hero_top';
    case HOME_CATEGORIES_BOTTOM = 'home_categories_bottom';
    case HOME_LATEST_SIDEBAR = 'home_latest_sidebar';
    case POST_ABOVE_TITLE = 'post_above_title';
    case POST_ABOVE_RELATED_POSTS = 'post_above_related_posts';
}
