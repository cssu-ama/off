<?php

namespace App\Services\V1\Authentication;

use App\Services\V1\Authentication\Interfaces\CaptchaServiceInterface;
use Exception;
use Mews\Captcha\Facades\Captcha;

/**
 * Class CaptchaService.
 */
class CaptchaService implements CaptchaServiceInterface
{
    public function loadCaptcha(): array
    {
        $captcha = Captcha::create('default', true);

        $image = is_object($captcha['img']) ? (string) $captcha['img'] : $captcha['img'];

        return [
            'key' => $captcha['key'],
            'image' => $image,
        ];
    }
}
