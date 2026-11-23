<?php

namespace App\Services\V1\Authentication;

use App\Jobs\V1\Authentication\SendOTPJob;
use App\Services\V1\Authentication\Interfaces\AuthenticationServiceInterface;
use App\Services\V1\User\Interfaces\UserServiceInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use Kavenegar\KavenegarApi;

/**
 * Class AuthenticationService.
 */
class AuthenticationService implements AuthenticationServiceInterface
{
    public function __construct(protected ?UserServiceInterface $userService = null)
    {
        $this->userService = $userService
            ?? app(UserServiceInterface::class);
    }

    public function send(string $receiver, string $code): bool
    {
        if (app()->isLocal()) {
            Log::info("Environment is LOCAL. SMS not sent to {$receiver}. OTP Code: {$code}");
        } else {
            $apiKey = config('services.kavenegar.key');
            $sender = config('services.kavenegar.sender');

            $message = 'کد ورود به پنل ادمین' . PHP_EOL .
                "code: $code" . PHP_EOL;

            $api = new KavenegarApi($apiKey);
            $api->Send($sender, $receiver, $message);

            Log::info("Environment is NOT PRODUCTION. SMS sent to {$receiver}. OTP Code: {$code}");
        }

        return true;
    }

    public function login(array $inputs): array
    {
        $user = $this
            ->userService
            ->showViaPhone($inputs['phone']);

        if (!$token = auth('api')->login($user)) {
            throw new Exception('خطا در ورود');
        }

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(): bool
    {
        $user = auth('api')
            ->user();

        if ($user) {
            auth('api')
                ->logout();

            return true;
        } else {
            throw new Exception('خطا در خروح از حساب');
        }
    }

    public function sendOTP(array $inputs): bool
    {
        SendOTPJob::dispatch($inputs['phone']);

        return true;
    }
}
