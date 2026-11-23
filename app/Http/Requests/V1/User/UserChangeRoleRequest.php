<?php

namespace App\Http\Requests\V1\User;

use App\Enums\V1\Role\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserChangeRoleRequest extends FormRequest
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
            'role' => [
                'required',
                'string',
                Rule::enum(UserRole::class)
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'role.required' => 'انتخاب نقش کاربر الزامی است.',
            'role.string' => 'نقش کاربر باید به صورت رشته باشد.',
            'role.enum' => 'نقش انتخاب‌شده معتبر نیست. نقش باید یکی از admin، writer یا manager باشد.',
        ];
    }
}
