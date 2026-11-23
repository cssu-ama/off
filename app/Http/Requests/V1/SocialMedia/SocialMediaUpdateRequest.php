<?php

namespace App\Http\Requests\V1\SocialMedia;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SocialMediaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'link' => ['sometimes', 'url', 'max:255'],
            'icon' => [
                'sometimes',
                'image:allow_svg',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:10240'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'عنوان باید یک متن معتبر باشد.',
            'title.max' => 'عنوان نباید بیشتر از ۲۵۵ کاراکتر باشد.',

            'link.url' => 'لینک واردشده معتبر نیست.',
            'link.max' => 'لینک نباید بیشتر از ۲۵۵ کاراکتر باشد.',

            'icon.image' => 'فایل انتخاب‌شده باید یک تصویر معتبر باشد.',
            'icon.mimes' => 'فرمت آیکون باید یکی از فرمت‌های JPG، JPEG، PNG، GIF یا SVG باشد.',
            'icon.max' => 'حجم آیکون نباید بیشتر از ۱۰ مگابایت باشد.',
        ];
    }
}
