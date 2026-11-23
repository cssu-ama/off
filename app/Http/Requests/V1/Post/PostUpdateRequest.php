<?php

namespace App\Http\Requests\V1\Post;

use App\Enums\V1\Post\PostStatus;
use App\Models\V1\Post\Post;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PostUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    private function maxContentBytes(): Closure
    {
        return function ($attribute, $value, $fail) {
            if (strlen($value) > 1_073_741_823) {
                $fail('حجم محتوا نباید بیشتر از ۱ گیگابایت باشد.');
            }
        };
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:700',
            ],

            'content' => [
                'sometimes',
                'string',
                $this->maxContentBytes(),
            ],

            'status' => [
                'sometimes',
                Rule::enum(PostStatus::class),
            ],

            'thumbnail' => [
                'sometimes',
                'nullable',
                'image:allow_svg',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:10240',
            ],

            'category_ids' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'category_ids.*' => [
                'exists:categories,id',
            ],

            'tag_ids' => [
                'sometimes',
                'nullable',
                'array',
            ],

            'tag_ids.*' => [
                'exists:tags,id',
            ],

            'is_top_headline' => [
                'sometimes',
                'nullable',
                'boolean'
            ],

            'is_headline' => [
                'sometimes',
                'nullable',
                'boolean'
            ],

            'is_specific' => [
                'sometimes',
                'nullable',
                'boolean'
            ],

            'order' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
                'max:' . (Post::query()->count()),
            ],
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.string' => 'عنوان باید به صورت متنی باشد.',
            'title.max' => 'عنوان نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',

            'description.string' => 'توضیحات باید به صورت متنی باشد.',
            'description.max' => 'توضیحات نمی‌تواند بیشتر از ۷۰۰ کاراکتر باشد.',

            'content.string' => 'محتوای پست باید به صورت متنی باشد.',
            'content.max' => 'محتوای پست نمی‌تواند بیشتر از ۵۰۰۰ کاراکتر باشد.',

            'status.enum' => 'وضعیت انتخاب‌شده معتبر نیست.',

            'thumbnail.image' => 'فایل تصویر بندانگشتی معتبر نیست.',
            'thumbnail.mimes' => 'فرمت تصویر بندانگشتی باید یکی از فرمت‌های jpg، jpeg، png، gif یا svg باشد.',
            'thumbnail.max' => 'حجم تصویر بندانگشتی نمی‌تواند بیشتر از 10 مگابایت باشد.',

            'category_ids.array' => 'شناسه دسته‌بندی‌ها باید به صورت آرایه ارسال شود.',
            'category_ids.*.exists' => 'یکی از دسته‌بندی‌های انتخاب‌شده وجود ندارد.',

            'tag_ids.array' => 'شناسه تگ‌ها باید به صورت آرایه ارسال شود.',
            'tag_ids.*.exists' => 'یکی از تگ‌های انتخاب‌شده وجود ندارد.',

            'is_top_headline.boolean' => 'این فیلد باید به صورت بولین (true/false) ارسال شود.',

            'is_headline.boolean' => 'این فیلد باید به صورت بولین (true/false) ارسال شود.',

            'is_specific.boolean' => 'این فیلد باید به صورت بولین (true/false) ارسال شود.',

            'order.integer' => 'ترتیب باید عدد باشد',
            'order.min' => 'ترتیب باید حداقل ۱ باشد.',
            'order.max' => 'ترتیب نمی‌تواند بیشتر از تعداد کل سوالات باشد.',
        ];
    }
}
