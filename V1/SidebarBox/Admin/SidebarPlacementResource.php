<?php

namespace App\Http\Resources\V1\SidebarBox\Admin;

use App\Enums\V1\SidebarBox\SidebarPlacement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SidebarPlacementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $labels = [
            SidebarPlacement::NONE->value => '',
            SidebarPlacement::HOME->value => 'صفحه اصلی',
            SidebarPlacement::SINGLE_POST->value => 'صفحه جزئیات مطلب',
        ];

        return [
            'value' => $this->value,
            'label' => $labels[$this->value],
        ];
    }
}
