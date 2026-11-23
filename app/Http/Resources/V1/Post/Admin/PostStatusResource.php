<?php

namespace App\Http\Resources\V1\Post\Admin;

use App\Enums\V1\Post\PostStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $labels = [
            PostStatus::PUBLISHED->value => 'منتشرشده',
            PostStatus::DRAFT->value => 'پیش نویس',
        ];

        return [
            'value' => $this->value,
            'label' => $labels[$this->value],
        ];
    }
}
