<?php

namespace App\Http\Resources\V1\Comment\Admin;

use App\Enums\V1\Comment\CommentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $labels = [
            CommentStatus::PUBLISHED->value => 'تایید شده',
            CommentStatus::REJECTED->value => 'تایید نشده',
            CommentStatus::PENDING->value => 'در انتظار تایید',
        ];
        return [
            'value' => $this->value,
            'label' => $labels[$this->value],
        ];
    }
}
