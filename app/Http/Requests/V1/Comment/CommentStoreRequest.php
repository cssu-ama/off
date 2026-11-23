<?php

namespace App\Http\Requests\V1\Comment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CommentStoreRequest extends FormRequest
{
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
            'comment' => [
                'required',
                'string',
                'max:5000',
            ],

            'post_id' => [
                'required',
                'integer',
                'exists:posts,id',
            ],

            'author_name' => [
                'nullable',
                'sometimes',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'sometimes',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'sometimes',
                'email',
                'max:255',
            ],

            'title' => [
                'nullable',
                'sometimes',
                'string',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'comment.required' => 'متن دیدگاه الزامی است.',
            'comment.string' => 'متن دیدگاه باید به‌صورت متن باشد.',
            'comment.max' => 'متن دیدگاه نمی‌تواند بیشتر از ۵۰۰۰ کاراکتر باشد.',

            'post_id.required' => 'انتخاب مطلب الزامی است.',
            'post_id.integer' => 'شناسه مطلب معتبر نیست.',
            'post_id.exists' => 'مطلب انتخاب‌شده یافت نشد.',

            'author_name.string' => 'نام واردشده معتبر نیست.',
            'author_name.max' => 'نام نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',

            'phone.string' => 'شماره موبایل معتبر نیست.',
            'phone.max' => 'شماره موبایل نمی‌تواند بیشتر از ۲۰ کاراکتر باشد.',

            'email.email' => 'فرمت آدرس ایمیل معتبر نیست.',
            'email.max' => 'آدرس ایمیل نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',

            'title.string' => 'عنوان دیدگاه باید به‌صورت متن باشد.',
            'title.max' => 'عنوان دیدگاه نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',
        ];
    }
}
