<?php

namespace App\Http\Resources\V1\Tag\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TagCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => TagResource::collection($this->collection),

            'meta' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
//                'from' => $this->firstItem(),
//                'to' => $this->lastItem(),
//                'first_page_url' => $this->url(1),
//                'last_page_url' => $this->url($this->lastPage()),
//                'next_page_url' => $this->nextPageUrl(),
//                'prev_page_url' => $this->previousPageUrl(),
//                'path' => $this->path(),
//                'links' => $this->linkCollection()->toArray(),
            ],
        ];
    }
}
