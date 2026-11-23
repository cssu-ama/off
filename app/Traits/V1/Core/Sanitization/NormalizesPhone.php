<?php

namespace App\Traits\V1\Core\Sanitization;

trait NormalizesPhone
{
    /**
     * @param string $phone
     * @return string
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '98')) {
            $phone = '0' . substr($phone, 2);
        }

        if (str_starts_with($phone, '9')) {
            $phone = '0' . $phone;
        }

        return $phone;
    }
}
