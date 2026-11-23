<?php

namespace App\Http\Resources\V1\Post\Admin;

use App\Http\Resources\V1\Core\File\MediaResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostSummaryResource extends JsonResource
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

            'thumbnail' => MediaResource::make($this->getMedia('thumbnail')->first()),

            'created_at' => Carbon::parse($this->created_at)
                ->timezone('Asia/Tehran')
                ->toIso8601String()
        ];
    }
}
