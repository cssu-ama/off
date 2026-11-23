<?php

namespace App\Rules\V1\Core\Time;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Translation\PotentiallyTranslatedString;

class TehranDateTimeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(
        string  $attribute,
        mixed   $value,
        Closure $fail
    ): void
    {
        $validator = Validator::make(
            ['value' => $value],
            [
                'value' => [
                    'date_format:Y-m-d\TH:i:sP',
                    'regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+03:30$/',
                ],
            ]
        );

        if ($validator->fails()) {
            $fail(
                'تاریخ و زمان باید معتبر باشد و با فرمت YYYY-MM-DDTHH:mm:ss+03:30 (منطقه زمانی آسیا/تهران) ارسال شود.'
            );
        }
    }
}
