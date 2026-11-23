<?php

namespace App\Http\Requests\V1\Tag;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TagUpdateRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:700'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'title.string' => 'عنوان باید به صورت متن وارد شود.',
            'title.max' => 'عنوان نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',

            'description.string' => 'توضیحات باید به صورت متن وارد شود.',
            'description.max' => 'توضیحات نمی‌تواند بیشتر از ۷۰۰ کاراکتر باشد.',
        ];
    }
}
