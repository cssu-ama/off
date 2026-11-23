<?php

namespace App\Services\V1\Authentication\Interfaces;

use Exception;

interface CaptchaServiceInterface
{
    /**
     * @throws Exception
     * @return array
     */
    public function loadCaptcha(): array;
}
