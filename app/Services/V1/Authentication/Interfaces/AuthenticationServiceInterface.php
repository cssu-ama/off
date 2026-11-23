<?php

namespace App\Services\V1\Authentication\Interfaces;

use AliArefAvin\AvinAuthPackage\Contracts\AvinAuthInterface;
use Exception;

interface AuthenticationServiceInterface extends AvinAuthInterface
{

    /**
     * @param string $receiver
     * @param string $code
     * @return bool
     */
    public function send(string $receiver, string $code): bool;

    /**
     * @param array $inputs
     * @return array
     * @throws Exception
     */
    public function login(array $inputs): array;

    /**
     * @return bool
     * @throws Exception
     */
    public function logout(): bool;

    /**
     * @param array $inputs
     * @return bool
     */
    public function sendOTP(array $inputs): bool;
}
