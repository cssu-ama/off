<?php

namespace App\Http\Requests\V1\Banner;

use App\Enums\V1\Banner\BannerStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BannerChangeStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::enum(BannerStatus::class)
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'انتخاب وضعیت بنر الزامی است.',
            'status.enum' => 'وضعیت انتخاب‌شده برای بنر معتبر نیست.',
        ];
    }
}
