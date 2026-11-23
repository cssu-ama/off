<?php

namespace App\Http\Resources\V1\Banner\Public;

use App\Http\Resources\V1\Core\File\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicBannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'link' => $this->link,
            'rel' => $this->rel,
            
            'banner_mobile' => MediaResource::make($this->getMedia('banner_mobile')->first()),
            'banner_tablet' => MediaResource::make($this->getMedia('banner_tablet')->first()),
            'banner_desktop' => MediaResource::make($this->getMedia('banner_desktop')->first()),
        ];
    }
}
