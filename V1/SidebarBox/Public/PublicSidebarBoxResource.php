<?php

namespace App\Http\Resources\V1\SidebarBox\Public;

use App\Http\Resources\V1\Post\Public\PublicPostSummaryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicSidebarBoxResource extends JsonResource
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
            'title' => $this->title,
            'placement' => $this->placement,

            'posts' => PublicPostSummaryResource::collection(
                $this->whenLoaded('posts')
            ),
        ];
    }
}
