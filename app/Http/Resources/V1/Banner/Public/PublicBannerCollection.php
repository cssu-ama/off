<?php

namespace App\Http\Resources\V1\Banner\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PublicBannerCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => PublicBannerResource::collection($this->collection),
            'meta' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
            ],
        ];
    }
}
