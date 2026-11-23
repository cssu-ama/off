<?php

namespace App\Jobs\V1\Authentication;

use AliArefAvin\AvinAuthPackage\Services\AvinAuthService;
use App\Services\V1\Authentication\Interfaces\AuthenticationServiceInterface;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendOTPJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $phone)
    {
        $this->queue = 'otp';
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(AvinAuthService $service, AuthenticationServiceInterface $authenticationService): void
    {
        $result = $service->sendOTP(
            $this->phone,
            null,
            $authenticationService
        );

        if (!$result['success']) {
            throw new Exception($result['message']);
        }
    }
}
