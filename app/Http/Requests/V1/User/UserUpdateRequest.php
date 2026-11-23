<?php

namespace App\Http\Requests\V1\User;

use App\Traits\V1\Core\Sanitization\NormalizesPhone;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    use NormalizesPhone;

    /**
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if (!empty($this->phone)) {
            $this->merge([
                'phone' => $this
                    ->normalizePhone($this->phone),
            ]);
        }
    }

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
        $userId =
            $this->route('user')?->id ??
            $this->route('user') ??
            $this->user()?->id;

        return [
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => [
                'sometimes',
                'min:8',
                'max:13',
                'regex:/^(?:\+98|0)?9\d{9}$/',
                Rule::unique('users', 'phone')
                    ->ignore($userId),
            ],
            'user_name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('users', 'user_name')
                    ->ignore($userId),
                'regex:/^[a-zA-Z][a-zA-Z0-9_]*$/',
            ],
            'avatar' => [
                'sometimes',
                'nullable',
                'image:allow_svg',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:10240',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'name.string' => 'نام باید به صورت متن وارد شود.',
            'name.max' => 'نام نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',

            'phone.min' => 'شماره تلفن باید حداقل ۸ کاراکتر باشد.',
            'phone.max' => 'شماره تلفن نمی‌تواند بیشتر از 13 کاراکتر باشد.',
            'phone.regex' => 'فرمت شماره تلفن وارد شده صحیح نیست.',
            'phone.unique' => 'شماره تلفن وارد شده قبلاً در سیستم ثبت شده است.',

            'user_name.string' => 'نام کاربری باید به صورت متن وارد شود.',
            'user_name.max' => 'نام کاربری نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',
            'user_name.unique' => 'نام کاربری وارد شده قبلاً در سیستم ثبت شده است.',
            'user_name.regex' => 'نام کاربری باید با یک حرف لاتین شروع شود و فقط شامل حروف لاتین، اعداد و آندرلاین (_) باشد. استفاده از فاصله و سایر کاراکترهای خاص مجاز نیست.',

            'avatar.image' => 'فایل تصویر آواتار معتبر نیست.',
            'avatar.mimes' => 'فرمت تصویر آواتار باید یکی از فرمت‌های jpg، jpeg، png، gif یا svg باشد.',
            'avatar.max' => 'حجم تصویر آواتار نمی‌تواند بیشتر از 10 مگابایت باشد.',
        ];
    }
}
