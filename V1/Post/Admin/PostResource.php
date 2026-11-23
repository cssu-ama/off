<?php

namespace App\Http\Resources\V1\Post\Admin;

use App\Http\Resources\V1\Category\Admin\CategoryResource;
use App\Http\Resources\V1\Core\File\MediaResource;
use App\Http\Resources\V1\Tag\Admin\TagResource;
use App\Http\Resources\V1\User\Admin\AuthorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class PostResource extends JsonResource
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
            'description' => $this->description,
            'content' => $this->content,
            'views' => $this->views,

            'reading_duration' => $this->reading_duration,

            'thumbnail' => MediaResource::make($this->getMedia('thumbnail')->first()),

            'categories' => CategoryResource::collection($this->categories),

            'tags' => TagResource::collection($this->tags),

            'status' => $this->status,

            'author' => AuthorResource::make($this->author),

            'is_top_headline' => $this->is_top_headline,

            'is_headline' => $this->is_headline,

            'is_specific' => $this->is_specific,

            'order' => $this->order,

            'created_at' => Carbon::parse($this->created_at)
                ->timezone('Asia/Tehran')
                ->toIso8601String()
        ];
    }
}
