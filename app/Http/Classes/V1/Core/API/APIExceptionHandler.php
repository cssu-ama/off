<?php

namespace App\Http\Classes\V1\Core\API;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final class APIExceptionHandler
{
    /**
     * @param Exceptions $exceptions
     * @return void
     */
    public static function registerUnauthorizedHandler(
        Exceptions $exceptions
    ): void
    {
        $exceptions->render(function (
            UnauthorizedHttpException $exception,
            Request                   $request
        ) {
            if (
                !$request->is('api/*') &&
                !$request->expectsJson()
            ) {
                return null;
            }

            $previous = $exception->getPrevious();
            $exceptionMessage = strtolower($exception->getMessage());

            $message = match (true) {
                $previous instanceof TokenExpiredException,
                str_contains($exceptionMessage, 'expired') =>
                'توکن احراز هویت منقضی شده است.',

                $previous instanceof TokenInvalidException =>
                'توکن احراز هویت نامعتبر است.',

                $previous instanceof JWTException =>
                'توکن احراز هویت ارسال نشده یا قابل پردازش نیست.',

                default =>
                'احراز هویت انجام نشد.',
            };

            return response()->json([
                'error' => true,
                'message' => $message,
            ], 401);
        });
    }

    /**
     * @param Exceptions $exceptions
     * @return void
     */
    public static function registerModelNotFoundHandler(
        Exceptions $exceptions
    ): void
    {
        $exceptions->render(function (
            NotFoundHttpException $exception,
            Request               $request
        ) {
            $previous = $exception->getPrevious();

            if (
                $previous instanceof ModelNotFoundException
            ) {
                return response()->json([
                    'error' => true,
                    'message' => 'رکورد موردنظر پیدا نشد.',
                ], 404);
            }

            return null;
        });
    }

    /**
     * @param Exceptions $exceptions
     * @return void
     */
    public static function configureJsonRendering(
        Exceptions $exceptions
    ): void
    {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request): bool => $request->is('api/*') || $request->expectsJson()
        );
    }
}
