<?php

namespace App\Http\Resources\V1\SidebarBox\Admin;

use App\Enums\V1\SidebarBox\PostSelectionType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SidebarPostSelectionTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $labels = [
            PostSelectionType::NONE->value => '',
            PostSelectionType::LATEST->value => 'جدیدترین مطالب',
            PostSelectionType::MANUAL->value => 'انتخاب دستی مطالب',
            PostSelectionType::MOST_VIEWED->value => 'پربازدیدترین مطالب',
        ];

        return [
            'value' => $this->value,
            'label' => $labels[$this->value],
        ];
    }
}
