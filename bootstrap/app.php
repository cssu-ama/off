<?php

use App\Http\Classes\V1\Core\API\APIExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use App\Http\Middleware\V1\Auth\JWTMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'jwt.auth' => JwtMiddleware::class,
            'EloquentFilter' => eloquentFilter\Facade\EloquentFilter::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        APIExceptionHandler::registerUnauthorizedHandler($exceptions);
        ApiExceptionHandler::registerModelNotFoundHandler($exceptions);
        ApiExceptionHandler::configureJsonRendering($exceptions);
    })->create();
