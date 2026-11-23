<?php

namespace App\Http\Resources\V1\Tag\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PublicTagListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => PublicTagResource::collection($this->collection),
        ];
    }
}
