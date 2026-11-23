<?php

namespace App\Http\Resources\V1\Comment\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,

            'title' => $this->title ?? '',
            'author_name' => $this->author_name ?: 'کاربر مجله',

            'children' => PublicCommentResource::collection($this->whenLoaded('publishedChildren')) ?? [],
        ];
    }
}
