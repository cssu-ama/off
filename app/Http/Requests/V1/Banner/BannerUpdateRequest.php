<?php

namespace App\Http\Requests\V1\Banner;

use App\Enums\V1\Banner\BannerPlacement;
use App\Enums\V1\Banner\BannerStatus;
use App\Enums\V1\Banner\LinkRelation;
use App\Rules\V1\Core\Time\TehranDateTimeRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BannerUpdateRequest extends FormRequest
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

            'placement' => [
                'sometimes',
                Rule::enum(BannerPlacement::class)
            ],

            'rel' => [
                'sometimes',
                Rule::enum(LinkRelation::class)
            ],

            'status' => [
                'sometimes',
                Rule::enum(BannerStatus::class)
            ],

            'banner_mobile' => [
                'sometimes',
                'image:allow_svg',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:10240',
            ],

            'banner_tablet' => [
                'sometimes',
                'image:allow_svg',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:10240',
            ],

            'banner_desktop' => [
                'sometimes',
                'image:allow_svg',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:10240',
            ],

            'start_at' => [
                'sometimes',
                'nullable',
                new TehranDateTimeRule(),
            ],

            'end_at' => [
                'sometimes',
                'nullable',
                new TehranDateTimeRule(),
                Rule::when(
                    $this->filled('start_at'),
                    ['after:start_at']
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'عنوان بنر باید از نوع متن باشد.',
            'title.max' => 'عنوان بنر نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',

            'link.url' => 'فرمت لینک واردشده معتبر نیست.',
            'link.max' => 'طول لینک نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',

            'placement.enum' => 'موقعیت نمایش بنر نامعتبر است. مقادیر مجاز: none، global_topbar، home_hero_top، home_categories_bottom، home_latest_sidebar، post_above_title، post_above_related_posts.',

            'rel.enum' => 'نوع رابطه لینک نامعتبر است. مقادیر مجاز: nofollow، follow، sponsored، ugc.',

            'status.enum' => 'وضعیت بنر نامعتبر است. مقادیر مجاز: inactive، active.',

            'banner_mobile.image' => 'فایل تصویر بندانگشتی معتبر نیست.',
            'banner_mobile.mimes' => 'فرمت تصویر بندانگشتی باید یکی از فرمت‌های jpg، jpeg، png، gif یا svg باشد.',
            'banner_mobile.max' => 'حجم تصویر بندانگشتی نمی‌تواند بیشتر از 10 مگابایت باشد.',

            'banner_tablet.image' => 'فایل تصویر بندانگشتی معتبر نیست.',
            'banner_tablet.mimes' => 'فرمت تصویر بندانگشتی باید یکی از فرمت‌های jpg، jpeg، png، gif یا svg باشد.',
            'banner_tablet.max' => 'حجم تصویر بندانگشتی نمی‌تواند بیشتر از 10 مگابایت باشد.',

            'banner_desktop.image' => 'فایل تصویر بندانگشتی معتبر نیست.',
            'banner_desktop.mimes' => 'فرمت تصویر بندانگشتی باید یکی از فرمت‌های jpg، jpeg، png، gif یا svg باشد.',
            'banner_desktop.max' => 'حجم تصویر بندانگشتی نمی‌تواند بیشتر از 10 مگابایت باشد.',

            'end_at.after' => 'زمان پایان باید بعد از زمان شروع باشد.',
        ];
    }
}
