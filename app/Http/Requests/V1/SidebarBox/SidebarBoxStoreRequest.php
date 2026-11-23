<?php

namespace App\Http\Requests\V1\SidebarBox;

use App\Enums\V1\SidebarBox\PostSelectionType;
use App\Enums\V1\SidebarBox\SidebarPlacement;
use App\Models\V1\SidebarBox\SidebarBox;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SidebarBoxStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'posts_limit' => $this->input('posts_limit') ?? 20,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $hasCategory = $this->filled('category_id');
        $hasTag = $this->filled('tag_id');

        $isManual = $this->input('post_selection_type')
            === PostSelectionType::MANUAL->value;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'sort_order' => [
                'sometimes',
                'integer',
                'min:1',
                'max:' . (SidebarBox::query()->count() + 1),
            ],

            'placement' => [
                'sometimes',
                'string',
                Rule::enum(SidebarPlacement::class),
            ],

            'post_selection_type' => [
                'sometimes',
                'string',
                Rule::enum(PostSelectionType::class),
                ...(($hasCategory || $hasTag) ? [
                    Rule::notIn([PostSelectionType::MANUAL->value]),
                ] : []),
            ],

            'category_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:categories,id',
                Rule::prohibitedIf($isManual || $hasTag),
            ],

            'tag_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:tags,id',
                Rule::prohibitedIf($isManual || $hasCategory),
            ],

            'posts_limit' => [
                'sometimes',
                'integer',
                'between:1,20',
            ],

            'post_ids' => [
                'sometimes',
                'nullable',
                'array',
                'max:20',
                Rule::prohibitedIf(!$isManual || $hasCategory || $hasTag),
            ],

            'post_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:posts,id',
            ],
        ];
    }

    /**
     * Get the custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'وارد کردن عنوان باکس سایدبار الزامی است.',
            'title.string' => 'عنوان باکس سایدبار باید از نوع رشته باشد.',
            'title.max' => 'عنوان باکس سایدبار نمی‌تواند بیشتر از 255 کاراکتر باشد.',

            'sort_order.integer' => 'ترتیب نمایش باید یک عدد صحیح باشد.',
            'sort_order.min' => 'ترتیب نمایش نمی‌تواند کمتر از 1 باشد.',
            'sort_order.max' => 'ترتیب نمایش نمی‌تواند بیشتر از کل تعداد باکس ها به علاوه یک باشد.',

            'placement.string' => 'محل نمایش باید از نوع رشته باشد.',
            'placement.enum' => 'محل نمایش انتخاب‌شده معتبر نیست.',

            'post_selection_type.string' => 'نوع انتخاب پست باید از نوع رشته باشد.',
            'post_selection_type.enum' => 'نوع انتخاب پست معتبر نیست.',
            'post_selection_type.not_in' => 'در صورت انتخاب دسته‌بندی یا برچسب، امکان انتخاب دستی پست‌ها وجود ندارد.',

            'category_id.integer' => 'شناسه دسته‌بندی باید یک عدد صحیح باشد.',
            'category_id.exists' => 'دسته‌بندی انتخاب‌شده وجود ندارد.',
            'category_id.prohibited' => 'در صورت انتخاب دستی پست‌ها یا انتخاب برچسب، امکان تعیین دسته‌بندی وجود ندارد.',

            'tag_id.integer' => 'شناسه برچسب باید یک عدد صحیح باشد.',
            'tag_id.exists' => 'برچسب انتخاب‌شده وجود ندارد.',
            'tag_id.prohibited' => 'در صورت انتخاب دستی پست‌ها یا انتخاب دسته‌بندی، امکان تعیین برچسب وجود ندارد.',

            'posts_limit.integer' => 'تعداد پست‌ها باید یک عدد صحیح باشد.',
            'posts_limit.between' => 'تعداد پست‌ها باید بین 1 تا 20 باشد.',

            'post_ids.array' => 'لیست پست‌های انتخاب‌شده باید به‌صورت آرایه ارسال شود.',
            'post_ids.max' => 'حداکثر امکان انتخاب 20 پست وجود دارد.',
            'post_ids.prohibited' => 'نحوه انتخاب پست ها باید دستی باشد',

            'post_ids.*.required' => 'شناسه پست الزامی است.',
            'post_ids.*.integer' => 'شناسه پست باید یک عدد صحیح باشد.',
            'post_ids.*.distinct' => 'انتخاب یک پست به‌صورت تکراری مجاز نیست.',
            'post_ids.*.exists' => 'یکی از پست‌های انتخاب‌شده وجود ندارد.',
        ];
    }
}
