<?php

use App\Http\Controllers\V1\Banner\BannerController;
use App\Http\Controllers\V1\Category\CategoryController;
use App\Http\Controllers\V1\Comment\CommentController;
use App\Http\Controllers\V1\Post\PostController;
use App\Http\Controllers\V1\SidebarBox\SidebarBoxController;
use App\Http\Controllers\V1\SocialMedia\SocialMediaController;
use App\Http\Controllers\V1\Tag\TagController;
use Illuminate\Support\Facades\Route;

// Post APIs
Route::get('/posts', [PostController::class, 'publicIndex']);
Route::get('/posts/{post:slug}', [PostController::class, 'publicShow']);

// Category APIs
Route::get('/categories', [CategoryController::class, 'publicIndex']);
Route::get('/categories/{category}', [CategoryController::class, 'publicShow']);

// Tag APIs
Route::get('/tags', [TagController::class, 'publicIndex']);
Route::get('/tags/{tag}', [TagController::class, 'publicShow']);

// Comments APIs
Route::get('/comments', [CommentController::class, 'publicIndex']);
Route::post('/comments', [CommentController::class, 'store']);
Route::post('/comments/replay-comment', [CommentController::class, 'replay']);

// Social Media APIs
Route::get('/social-medias', [SocialMediaController::class, 'publicIndex']);

// Banner APIs
Route::get('/banners', [BannerController::class, 'publicIndex']);

// Sidebar Boxes APIs
Route::get('/sidebar-boxes', [SidebarBoxController::class, 'publicIndex']);
