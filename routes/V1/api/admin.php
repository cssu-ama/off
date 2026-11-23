<?php

use App\Http\Controllers\V1\Authentication\AuthenticationController;
use App\Http\Controllers\V1\Authentication\CaptchaController;
use App\Http\Controllers\V1\Banner\BannerController;
use App\Http\Controllers\V1\Category\CategoryController;
use App\Http\Controllers\V1\Comment\CommentController;
use App\Http\Controllers\V1\Core\Media\MediaController;
use App\Http\Controllers\V1\Post\PostController;
use App\Http\Controllers\V1\Role\RoleController;
use App\Http\Controllers\V1\SidebarBox\SidebarBoxController;
use App\Http\Controllers\V1\SocialMedia\SocialMediaController;
use App\Http\Controllers\V1\Tag\TagController;
use App\Http\Controllers\V1\User\UserController;
use Illuminate\Support\Facades\Route;

// Authentication
Route::post('/send-otp', [AuthenticationController::class, 'sendOTP']);
Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/logout', [AuthenticationController::class, 'logout']);

Route::get('/load-captcha', [CaptchaController::class, 'loadCaptcha']);

Route::middleware('jwt.auth')->group(function () {

    // User manipulation APIs
    Route::put('/users/{user}/change-role', [UserController::class, 'changeRole']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    // Role manipulation APIs
    Route::get('/roles', [RoleController::class, 'index']);

    // Media manipulation APIs
    Route::get('/medias/types', [MediaController::class, 'indexTypes']);

    Route::get('/medias', [MediaController::class, 'index']);
    Route::get('/medias/{media}', [MediaController::class, 'show']);
    Route::post('/medias', [MediaController::class, 'store']);
    Route::delete('/medias/{media}', [MediaController::class, 'destroy']);

    // Category manipulation APIs
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // Tag manipulation APIs
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/{tag}', [TagController::class, 'show']);
    Route::post('/tags', [TagController::class, 'store']);
    Route::put('/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);

    // Post manipulation APIs
    Route::put('/posts/change-order', [PostController::class, 'changeOrder']);

    Route::get('/posts/get-status', [PostController::class, 'indexStatus']);
    Route::put('/posts/{post}/change-status', [PostController::class, 'changeStatus']);

    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    // Comment manipulation APIs
    Route::get('/comments/get-status', [CommentController::class, 'indexStatus']);
    Route::put('/comments/{comment}/change-status', [CommentController::class, 'changeStatus']);

    Route::get('/comments', [CommentController::class, 'index']);
    Route::get('/comments/{comment}', [CommentController::class, 'show']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::post('/comments/replay-comment', [CommentController::class, 'replay']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // Social Media manipulation APIs
    Route::get('/social-medias', [SocialMediaController::class, 'index']);
    Route::get('/social-medias/{socialMedia}', [SocialMediaController::class, 'show']);
    Route::post('/social-medias', [SocialMediaController::class, 'store']);
    Route::put('/social-medias/{socialMedia}', [SocialMediaController::class, 'update']);
    Route::delete('/social-medias/{socialMedia}', [SocialMediaController::class, 'destroy']);

    // Banner manipulation APIs
    Route::get('/banners/get-status', [BannerController::class, 'indexStatus']);
    Route::put('/banners/{banner}/change-status', [BannerController::class, 'changeStatus']);

    Route::get('/banners/get-placements', [BannerController::class, 'indexPlacement']);
    Route::get('/banners/get-link-relations', [BannerController::class, 'indexLinkRelation']);

    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/banners/{banner}', [BannerController::class, 'show']);
    Route::post('/banners', [BannerController::class, 'store']);
    Route::put('/banners/{banner}', [BannerController::class, 'update']);
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy']);

    // Sidebar Box manipulation APIs
    Route::put('/sidebar-boxes/change-order', [SidebarBoxController::class, 'changeOrder']);
    Route::put('/sidebar-boxes/{sidebarBox}/change-post-order', [SidebarBoxController::class, 'changePostOrder']);

    Route::get('/sidebar-boxes/get-placements', [SidebarBoxController::class, 'indexPlacement']);
    Route::get('/sidebar-boxes/get-post-selection-type', [SidebarBoxController::class, 'indexPostSelectionType']);

    Route::get('/sidebar-boxes', [SidebarBoxController::class, 'index']);
    Route::get('/sidebar-boxes/{sidebarBox}', [SidebarBoxController::class, 'show']);
    Route::post('/sidebar-boxes', [SidebarBoxController::class, 'store']);
    Route::put('/sidebar-boxes/{sidebarBox}', [SidebarBoxController::class, 'update']);
    Route::delete('/sidebar-boxes/{sidebarBox}', [SidebarBoxController::class, 'destroy']);
});
