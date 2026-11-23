<?php

namespace App\Providers;

use App\Services\V1\Authentication\AuthenticationService;
use App\Services\V1\Authentication\CaptchaService;
use App\Services\V1\Authentication\Interfaces\AuthenticationServiceInterface;
use App\Services\V1\Authentication\Interfaces\CaptchaServiceInterface;
use App\Services\V1\Banner\BannerService;
use App\Services\V1\Banner\Interfaces\BannerServiceInterface;
use App\Services\V1\Category\CategoryService;
use App\Services\V1\Category\Interfaces\CategoryServiceInterface;
use App\Services\V1\Comment\CommentService;
use App\Services\V1\Comment\Interfaces\CommentServiceInterface;
use App\Services\V1\Core\Media\Interfaces\MediaServiceInterface;
use App\Services\V1\Core\Media\MediaService;
use App\Services\V1\Post\Interfaces\PostServiceInterface;
use App\Services\V1\Post\PostService;
use App\Services\V1\Role\Interfaces\RoleServiceInterface;
use App\Services\V1\Role\RoleService;
use App\Services\V1\SidebarBox\Interfaces\SidebarBoxServiceInterface;
use App\Services\V1\SidebarBox\SidebarBoxService;
use App\Services\V1\SocialMedia\Interfaces\SocialMediaServiceInterface;
use App\Services\V1\SocialMedia\SocialMediaService;
use App\Services\V1\Tag\Interfaces\TagServiceInterface;
use App\Services\V1\Tag\TagService;
use App\Services\V1\User\Interfaces\UserServiceInterface;
use App\Services\V1\User\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Authentication
        $this->app->bind(AuthenticationServiceInterface::class, AuthenticationService::class);
        $this->app->bind(CaptchaServiceInterface::class, CaptchaService::class);

        // User
        $this->app->bind(UserServiceInterface::class, UserService::class);

        // Role
        $this->app->bind(RoleServiceInterface::class, RoleService::class);

        // Post
        $this->app->bind(PostServiceInterface::class, PostService::class);

        // Media
        $this->app->bind(MediaServiceInterface::class, MediaService::class);

        // Category
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);

        // Tag
        $this->app->bind(TagServiceInterface::class, TagService::class);

        // Comment
        $this->app->bind(CommentServiceInterface::class, CommentService::class);

        // Social Media
        $this->app->bind(SocialMediaServiceInterface::class, SocialMediaService::class);

        // Banner
        $this->app->bind(BannerServiceInterface::class, BannerService::class);

        // Sidebar Box
        $this->app->bind(SidebarBoxServiceInterface::class, SidebarBoxService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
