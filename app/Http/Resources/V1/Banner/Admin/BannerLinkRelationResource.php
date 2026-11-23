<?php

namespace App\Http\Resources\V1\Banner\Admin;

use App\Enums\V1\Banner\LinkRelation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerLinkRelationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $labels = [
            LinkRelation::NOFOLLOW->value => 'عدم انتقال اعتبار سئو (Nofollow)',
            LinkRelation::FOLLOW->value => 'انتقال اعتبار سئو (Follow)',
            LinkRelation::SPONSORED->value => 'لینک تبلیغاتی یا حمایت‌شده (Sponsored)',
            LinkRelation::UGC->value => 'لینک محتوای تولیدشده توسط کاربران (UGC)',
        ];

        return [
            'value' => $this->value,
            'label' => $labels[$this->value],
        ];
    }
}
