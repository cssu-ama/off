<?php

namespace App\Http\Requests\V1\Authentication\Login;

use App\Traits\V1\Core\Sanitization\NormalizesPhone;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PublicLoginRequest extends FormRequest
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
            ],
            'code' => ['required', 'avin_verify:phone'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'شماره تلفن الزامی است.',
            'phone.min' => 'شماره تلفن باید حداقل ۸ کاراکتر باشد.',
            'phone.max' => 'شماره تلفن نمی‌تواند بیشتر از 13 کاراکتر باشد.',
            'phone.regex' => 'فرمت شماره تلفن وارد شده صحیح نیست.',

            'code.required' => 'کد تأیید الزامی است.',
            'code.avin_verify' => 'کد تأیید وارد شده صحیح یا معتبر نیست.',
        ];
    }
}
