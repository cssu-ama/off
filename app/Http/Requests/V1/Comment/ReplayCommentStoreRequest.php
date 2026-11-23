<?php

namespace App\Http\Requests\V1\Comment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReplayCommentStoreRequest extends FormRequest
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

            'parent_id' => [
                'required',
                'integer',
                'exists:comments,id',
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


    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'comment.required' => 'وارد کردن متن دیدگاه الزامی است.',
            'comment.string' => 'متن دیدگاه باید از نوع رشته باشد.',
            'comment.max' => 'متن دیدگاه نمی‌تواند بیشتر از ۵۰۰۰ کاراکتر باشد.',

            'post_id.required' => 'انتخاب نوشته الزامی است.',
            'post_id.integer' => 'شناسه نوشته باید یک عدد صحیح باشد.',
            'post_id.exists' => 'نوشته انتخاب‌شده وجود ندارد.',

            'parent_id.required' => 'شناسه دیدگاه والد الزامی است.',
            'parent_id.integer' => 'شناسه دیدگاه والد باید یک عدد صحیح باشد.',
            'parent_id.exists' => 'دیدگاه والد انتخاب‌شده وجود ندارد.',

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
