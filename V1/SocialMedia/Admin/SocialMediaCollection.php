<?php

namespace App\Http\Resources\V1\SocialMedia\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SocialMediaCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => SocialMediaResource::collection($this->collection),
            'meta' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
            ],
        ];
    }
}
