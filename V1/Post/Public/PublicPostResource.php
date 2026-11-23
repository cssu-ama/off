<?php

namespace App\Http\Resources\V1\Post\Public;

use App\Http\Resources\V1\Category\Public\PublicCategoryResource;
use App\Http\Resources\V1\Core\File\MediaResource;
use App\Http\Resources\V1\Tag\Admin\TagResource;
use App\Http\Resources\V1\User\Public\PublicAuthorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class PublicPostResource extends JsonResource
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
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,

            'reading_duration' => $this->reading_duration,

            'thumbnail' => MediaResource::make($this->getMedia('thumbnail')->first()),

            'categories' => PublicCategoryResource::collection($this->categories),

            'tags' => TagResource::collection($this->tags),

            'author' => PublicAuthorResource::make($this->author),

            'is_top_headline' => $this->is_top_headline,

            'is_headline' => $this->is_headline,

            'is_specific' => $this->is_specific,

            'created_at' => Carbon::parse($this->created_at)
                ->timezone('Asia/Tehran')
                ->toIso8601String()
        ];
    }
}
