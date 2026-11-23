<?php

namespace App\Http\Resources\V1\SidebarBox\Admin;

use App\Http\Resources\V1\Category\Admin\CategoryResource;
use App\Http\Resources\V1\Post\Admin\PostSummaryResource;
use App\Http\Resources\V1\Tag\Admin\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SidebarBoxResource extends JsonResource
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
            'order' => $this->sort_order,
            'placement' => $this->placement,
            'post_selection_type' => $this->post_selection_type,
            'posts_limit' => $this->posts_limit,

            'categories' => CategoryResource::make($this->category),

            'tags' => TagResource::make($this->tag),

            'posts' => PostSummaryResource::collection(
                $this->whenLoaded('posts')
            ),
        ];
    }
}
