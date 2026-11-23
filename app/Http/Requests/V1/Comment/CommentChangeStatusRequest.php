<?php

namespace App\Http\Requests\V1\Comment;

use App\Enums\V1\Comment\CommentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CommentChangeStatusRequest extends FormRequest
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
                Rule::enum(CommentStatus::class)
            ]
        ];
    }


    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'status.required' => 'انتخاب وضعیت دیدگاه الزامی است.',
            'status.enum' => 'وضعیت انتخاب‌شده برای دیدگاه معتبر نیست.',
        ];
    }
}
