<?php

namespace App\Http\Controllers\V1\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Authentication\Login\LoginRequest;
use App\Http\Requests\V1\Authentication\OTP\OTPRequest;
use App\Http\Resources\V1\User\Admin\UserResource;
use App\Services\V1\Authentication\Interfaces\AuthenticationServiceInterface;
use Exception;
use Illuminate\Http\JsonResponse;

class AuthenticationController extends Controller
{
    /**
     * @param AuthenticationServiceInterface $authenticationService
     */
    public function __construct(protected AuthenticationServiceInterface $authenticationService)
    {
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        try {
            $inputs = $request
                ->only([
                    'phone',
                    'code'
                ]);

            [$user, $token] = array_values(
                $this->authenticationService
                    ->login($inputs)
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => UserResource::make($user),
                    'token' => $token,
                ],
                'message' => 'با موفقیت وارد شدید'
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        try {
            $this
                ->authenticationService
                ->logout();

            return response()->json([
                'success' => true,
                'message' => 'با موفقیت خارج شدید'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param OTPRequest $request
     * @return JsonResponse
     */
    public function sendOTP(OTPRequest $request)
    {
        try {
            $inputs = $request
                ->only([
                    'phone',
                    'key',
                    'captcha',
                ]);

            $this
                ->authenticationService
                ->sendOTP($inputs);

            return response()->json([
                'success' => true,
                'message' => 'کد تایید در حال ارسال است...'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
