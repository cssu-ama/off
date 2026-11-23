<?php

namespace App\Http\Resources\V1\Banner\Admin;

use App\Enums\V1\Banner\BannerStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $labels = [
            BannerStatus::INACTIVE->value => 'غیرفعال',
            BannerStatus::ACTIVE->value => 'فعال',
        ];

        return [
            'value' => $this->value,
            'label' => $labels[$this->value],
        ];
    }
}
