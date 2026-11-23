<?php

namespace App\Http\Requests\V1\Authentication\OTP;

use App\Traits\V1\Core\Sanitization\NormalizesPhone;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OTPRequest extends FormRequest
{
    use NormalizesPhone;

    /**
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('phone')) {
            $this->merge([
                'phone' => $this->normalizePhone($this->phone),
            ]);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'min:8',
                'max:13',
                'regex:/^(?:\+98|0)?9\d{9}$/',
                'exists:users,phone',
            ],
            'key' => [
                'required',
                'string'
            ],
            'captcha' => [
                'required',
                'captcha_api:' . request()->key
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'وارد کردن شماره تلفن الزامی است',
            'phone.min' => 'شماره تلفن باید حداقل ۸ رقم باشد',
            'phone.max' => 'شماره تلفن نمی‌تواند بیشتر از 13 رقم باشد',
            'phone.regex' => 'فرمت شماره تلفن صحیح نیست',
            'phone.exists' => 'مشکل در ورود به وجود آمده است',

            'key.required' => 'مشکلی در ورود به وجود آمده است',
            'key.string' => 'مشکلی در ورود به وجود آمده است',

            'captcha.required' => 'کپچا الزامی است',
            'captcha.captcha_api' => 'کپچا اشتباه است',
        ];
    }
}
