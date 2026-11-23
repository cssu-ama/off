<?php

namespace App\Http\Controllers\V1\Authentication;

use App\Http\Controllers\Controller;
use App\Services\V1\Authentication\Interfaces\CaptchaServiceInterface;
use Exception;

class CaptchaController extends Controller
{
    public function __construct(protected CaptchaServiceInterface $captchaService)
    {
    }

    public function loadCaptcha()
    {
        try {
            [$key, $image] = array_values(
                $this->captchaService->loadCaptcha()
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'key' => $key,
                    'image' => $image,
                ],
                'message' => 'کپچا ایجاد شد'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
