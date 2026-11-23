<?php

namespace App\Http\Resources\V1\SocialMedia\Admin;

use App\Http\Resources\V1\Core\File\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialMediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'link' => $this->link,
            'icon' => MediaResource::make($this->getMedia('icon')->first()),
        ];
    }
}
