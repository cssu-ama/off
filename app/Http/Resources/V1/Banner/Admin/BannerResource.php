<?php

namespace App\Http\Resources\V1\Banner\Admin;

use App\Http\Resources\V1\Core\File\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'link' => $this->link,
            'placement' => $this->placement,
            'rel' => $this->rel,
            'status' => $this->status,

            'banner_mobile' => MediaResource::make($this->getMedia('banner_mobile')->first()),
            'banner_tablet' => MediaResource::make($this->getMedia('banner_tablet')->first()),
            'banner_desktop' => MediaResource::make($this->getMedia('banner_desktop')->first()),

            'start_at' => $this->start_at
                ?->setTimezone('Asia/Tehran')
                ->format('Y-m-d\TH:i:sP'),

            'end_at' => $this->end_at
                ?->setTimezone('Asia/Tehran')
                ->format('Y-m-d\TH:i:sP'),
        ];
    }
}
