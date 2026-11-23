<?php

namespace App\Http\Resources\V1\Banner\Admin;

use App\Enums\V1\Banner\BannerPlacement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerPlacementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $labels = [
            BannerPlacement::NONE->value => 'غیرفعال',
            BannerPlacement::POST_ABOVE_TITLE->value => 'بالای عنوان مطلب',
            BannerPlacement::POST_ABOVE_RELATED_POSTS->value => 'بالای مطالب مرتبط',
            BannerPlacement::HOME_LATEST_SIDEBAR->value => 'ستون کناری آخرین مطالب صفحه اصلی',
            BannerPlacement::HOME_HERO_TOP->value => 'بالای بخش اصلی صفحه اصلی',
            BannerPlacement::HOME_CATEGORIES_BOTTOM->value => 'پایین دسته‌بندی‌های صفحه اصلی',
            BannerPlacement::GLOBAL_TOPBAR->value => 'نوار بالایی تمام صفحات',
        ];

        return [
            'value' => $this->value,
            'label' => $labels[$this->value],
        ];
    }
}
